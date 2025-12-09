<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Facades\JWTFactory;
use Tymon\JWTAuth\Exceptions\JWTException;

class AuthController extends Controller
{
    /**
     * Check user authentication using web service
     */
    protected function allowed_user_using_web_service($username, $password)
    {
        try {
            // Prepare data for the request
            $data = [
                'n' => $username,
                'p' => $password
            ];

            // Send a POST request to the web service
            $response = Http::timeout(30)->asForm()->post('https://service.unisayogya.ac.id/loginall.php', $data);

            // dd($response->body());
            // Check if the request was successful
            if ($response->failed()) {
                throw new \Exception('Failed to connect to the authentication web service. HTTP Status: ' . $response->status());
            }

            // Decode the JSON response
            $json = $response->json();

            // Validate response structure
            if (!is_array($json)) {
                throw new \Exception('Invalid JSON response from authentication service.');
            }

            // Check if the 'isallowed' field exists
            if (!isset($json['isallowed'])) {
                throw new \Exception('Authentication service response is missing required fields.');
            }

            return $json;
        } catch (\Throwable $th) {
            // Log the error for debugging
            Log::error('Authentication service error', [
                'username' => $username,
                'error' => $th->getMessage(),
            ]);

            // Return false to indicate failure
            return false;
        }
    }

    /**
     * Generate authentication token
     */
    protected function generateToken($remember, $data, $loginAs)
    {
        try {
            // Calculate TTL (Time To Live)
            $ttl = $remember ? 43200 : 1440; // 30 days if remember me, otherwise 1 day (in minutes)

            // Prepare user data based on login type
            $userData = [
                'login_as' => $loginAs,
                'expires_at' => now()->addMinutes($ttl)->timestamp
            ];

            // Set data based on login type
            if ($loginAs == 'mahasiswa') {
                $userData['username'] = $data->nim ?? null;
                $userData['name'] = $data->namalengkap ?? null;
                $userData['email'] = null; // mh_v_nama tidak punya email
                $userData['role'] = 'mahasiswa';
            } else if ($loginAs == 'tendik') {
                $userData['id'] = $data->kdperson ?? null;
                $userData['username'] = $data->nip ?? null;
                $userData['name'] = $data->nama ?? null;
                $userData['email'] = $data->email ?? null;
                $userData['role'] = $data->role ?? 'admin_akademik';
                $userData['kdunitkerja'] = $data->kdunitkerja ?? null;
                $userData['nip'] = $data->nip ?? null;
            } else if ($loginAs == 'dosen') {
                $userData['id'] = $data->kdperson ?? null;
                $userData['username'] = $data->kodeuser ?? null;
                $userData['name'] = $data->nama ?? null;
                $userData['email'] = $data->email ?? null;
                $userData['role'] = $data->role ?? 'dosen_pembimbing';
                $userData['kdunitkerja'] = $data->kdunitkerja ?? null;
            } else if ($loginAs == 'preceptor') {
                $userData['id'] = $data->kd_preceptor ?? null;
                $userData['username'] = $data->kode_preceptor ?? null;
                $userData['name'] = $data->nama_preceptor ?? null;
                $userData['email'] = $data->email ?? null;
                $userData['role'] = 'preceptor';
                $userData['kd_dokubisa_lahan'] = $data->kd_dokubisa_lahan ?? null;
                $userData['nama_lahan'] = $data->nama_lahan ?? null;
                $userData['telepon'] = $data->telepon ?? null;
                $userData['alamat'] = $data->alamat ?? null;
            }

            $claims = array_merge($userData, [
                'sub' => $userData['username'] ?? Str::random(16),
            ]);

            // Set TTL temporarily for this token
            $factory = JWTAuth::factory();
            $previousTtl = $factory->getTTL();
            $factory->setTTL($ttl);

            $payload = JWTFactory::customClaims($claims)->make();
            $token = JWTAuth::encode($payload)->get();

            // restore default TTL
            $factory->setTTL($previousTtl);

            return [
                'token' => $token,
                'ttl' => $ttl * 60 // Convert to seconds
            ];
        } catch (\Throwable $e) {
            Log::error('Token generation error', [
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Process login request
     */
    public function processLogin(Request $request)
    {
        // Validate the request (allow NIM or NIP)
        $validator = Validator::make($request->all(), [
            'nim' => 'required_without:nip',
            'nip' => 'required_without:nim',
            'password' => 'required',
            'remember_me' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'code' => 'BAD_REQUEST',
                'errors' => $validator->errors()->toArray(),
            ], 400);
        }

        // Prepare variables
        $username = $request->get('nim') ?? $request->get('nip');
        $credentials = [
            'nim' => $username,
            'password' => $request->get('password'),
        ];
        $remember = $request->get('remember_me', false);

        try {
            // Step 1: Check user using web service
            $checkUserResponse = $this->allowed_user_using_web_service($username, $credentials['password']);

            // Step 2: Handle web service response
            if ($checkUserResponse === false) {
                // Web service is down, return error
                return response()->json([
                    'success' => false,
                    'code' => 'SERVICE_UNAVAILABLE',
                    'message' => 'Authentication service is currently unavailable. Please try again later.',
                ], 503);
            }

            // Step 3: Validate response structure
            if (!is_array($checkUserResponse) || !isset($checkUserResponse['isallowed'])) {
                return response()->json([
                    'success' => false,
                    'code' => 'INVALID_RESPONSE',
                    'message' => 'Invalid response from authentication service',
                ], 500);
            }

            // Step 4: Check if user is allowed
            if (!$checkUserResponse['isallowed']) {
                return response()->json([
                    'success' => false,
                    'code' => 'UNAUTHORIZED',
                    'message' => 'Invalid credentials provided',
                ], 401);
            }

            // Step 5: Get login type from web service response
            $loginAs = $checkUserResponse['loginas'] ?? 'mahasiswa';

            // Step 6: Get user data from database based on login type
            $data = null;

            try {
                if ($loginAs == 'mahasiswa') {
                    // ✅ PERBAIKAN: Menggunakan mh_v_nama
                    $data = DB::table('mh_v_nama as m')
                        ->where('m.nim', $username)
                        ->select(
                            // 'm.kdperson',
                            // 'm.kdmahasiswa',
                            // 'm.kdtamasuk',
                            'm.nim',
                            'm.namalengkap',
                            // 'm.nik',
                            // 'm.tempatlahir',
                            // 'm.tanggallahir'
                        )
                        ->first();
                } else if ($loginAs == 'tendik') {
                    // ✅ Query untuk tendik tetap sama
                    $data = DB::table('vsdm_pegawai01 as p')
                        ->where('p.nip', $username)
                        ->first();
                } else if ($loginAs == 'dosen') {
                    // Query untuk dosen
                    $data = DB::table('v_dokubisa_dosen as d')
                        ->where('d.kodeuser', $username)
                        ->first();
                } else {
                    // Default to preceptor
                    $loginAs = "preceptor";
                    $data = DB::table('ak_dokubisa_preceptor as p')
                        ->whereNull('p.deleted_at')
                        ->join('ak_dokubisa_lahan as l', 'l.kd_dokubisa_lahan', '=', 'p.kd_dokubisa_lahan')
                        ->where('p.kode_preceptor', $username)
                        ->select(
                            'p.kd_preceptor',
                            'p.nama_preceptor',
                            'p.email',
                            'p.kode_preceptor',
                            'p.telepon',
                            'p.alamat',
                            'l.kd_dokubisa_lahan',
                            'l.nama_lahan'
                        )
                        ->first();
                }
            } catch (\Illuminate\Database\QueryException $e) {
                Log::error('Database query error during login', [
                    'username' => $username,
                    'login_as' => $loginAs,
                    'error' => $e->getMessage(),
                ]);

                return response()->json([
                    'success' => false,
                    'code' => 'DATABASE_ERROR',
                    'message' => 'Error retrieving user data from database',
                ], 500);
            }

            // Step 7: Check if user exists in database
            if (!$data) {
                return response()->json([
                    'success' => false,
                    'code' => 'USER_NOT_FOUND',
                    'message' => 'User not found in database',
                ], 404);
            }

            // Step 8: Set user role
            if ($loginAs == 'dosen' || $loginAs == 'tendik') {
                $getRole = DB::table('ak_dokubisa_role')->where('kd_user', '=', $data->kdperson)->first();

                if ($getRole) {
                    $data->role = $getRole->role;
                } else {
                    // Create new role if doesn't exist
                    $id = DB::table('ak_dokubisa_role')->insertGetId([
                        'role' => $loginAs == 'tendik' ? 'admin_akademik' : 'dosen_pembimbing',
                        'login_as' => $loginAs,
                        'kd_user' => $data->kdperson,
                        'kd_unitkerja' => $data->kdunitkerja ?? null,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);

                    $dataRole = DB::table('ak_dokubisa_role')->where('kd_dokubisa_role', $id)->first();
                    $data->role = $dataRole->role;
                }
            } else if ($loginAs == 'mahasiswa') {
                // ✅ Role mahasiswa
                $data->role = 'mahasiswa';
            } else if ($loginAs == 'preceptor') {
                $data->role = 'preceptor';
            }

            // Set loginAs property
            $data->loginAs = $loginAs;

            // Step 9: Generate token
            $tokenResponse = $this->generateToken($remember, $data, $loginAs);

            if ($tokenResponse === false || !is_array($tokenResponse) || !isset($tokenResponse['token']) || !isset($tokenResponse['ttl'])) {
                return response()->json([
                    'success' => false,
                    'code' => 'TOKEN_GENERATION_ERROR',
                    'message' => 'Failed to generate authentication token',
                ], 500);
            }

            // Step 10: Prepare response data
            $responseData = [
                'isallowed' => true,
                'token' => $tokenResponse['token'],
                'token_type' => 'bearer',
                'timeout' => $tokenResponse['ttl'],
                'loginas' => $loginAs,
                'username' => $data->nim ?? $data->kodeuser ?? $data->nip ?? null,
                'role' => $data->role ?? $loginAs,
            ];

            // Add namalengkap for mahasiswa
            if ($loginAs === 'mahasiswa' && isset($data->namalengkap)) {
                $responseData['namalengkap'] = $data->namalengkap;
            }
            // Add name for other roles
            elseif (isset($data->nama)) {
                $responseData['name'] = $data->nama;
            }

            return response()->json($responseData, 200);
        } catch (\Throwable $e) {
            Log::error('Authentication process error', [
                'username' => $username ?? 'unknown',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'code' => 'INTERNAL_SERVER_ERROR',
                'message' => 'An error occurred during authentication. Please try again later.',
            ], 500);
        }
    }

    /**
     * Get current user data
     */
    public function me(Request $request)
    {
        try {
            $token = JWTAuth::parseToken();
            $payload = $token->getPayload()->toArray();

            return response()->json([
                'success' => true,
                'data' => $payload
            ], 200);
        } catch (JWTException $e) {
            return response()->json([
                'success' => false,
                'code' => 'UNAUTHORIZED',
                'message' => 'User not authenticated. Please login first.',
            ], 401);
        } catch (\Throwable $e) {
            Log::error('Get user data error', [
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'code' => 'INTERNAL_SERVER_ERROR',
                'message' => 'An error occurred while fetching user data',
            ], 500);
        }
    }

    /**
     * Refresh token (extend session)
     */
    public function refresh(Request $request)
    {
        try {
            $newToken = JWTAuth::parseToken()->refresh();
            $ttl = config('jwt.ttl', 60);

            return response()->json([
                'success' => true,
                'token_type' => 'bearer',
                'expires_in' => $ttl * 60,
                'token' => $newToken
            ], 200);
        } catch (JWTException $e) {
            return response()->json([
                'success' => false,
                'code' => 'UNAUTHORIZED',
                'message' => 'User not authenticated. Please login first.',
            ], 401);
        } catch (\Throwable $e) {
            Log::error('Token refresh error', [
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'code' => 'INTERNAL_SERVER_ERROR',
                'message' => 'An error occurred while refreshing token',
            ], 500);
        }
    }

    /**
     * Logout user
     */
    public function logout(Request $request)
    {
        try {
            $token = JWTAuth::getToken();

            if ($token) {
                JWTAuth::invalidate($token);
            }

            return response()->json([
                'success' => true,
                'message' => 'Successfully logged out'
            ], 200);
        } catch (JWTException $e) {
            return response()->json([
                'success' => true,
                'message' => 'Successfully logged out'
            ], 200);
        } catch (\Throwable $e) {
            Log::error('Logout error', [
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'code' => 'LOGOUT_ERROR',
                'message' => 'An error occurred during logout',
            ], 500);
        }
    }
}
