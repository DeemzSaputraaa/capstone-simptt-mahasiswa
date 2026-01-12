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

            Log::info('Authentication service response', [
                'username' => $username,
                'status' => $response->status(),
                'json' => $json,
            ]);

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
                $userData['name'] = $data->namalengkap ?? $data->nama ?? null;
                $userData['email'] = $data->email ?? null;
                $userData['role'] = 'tendik';
                $userData['kdunitkerja'] = $data->kdunitkerja ?? $data->unitkerja ?? null;
                $userData['nip'] = $data->nip ?? null;
            } else {
                throw new \Exception('Unsupported login type');
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
            $allowedLoginTypes = ['mahasiswa', 'tendik'];
            if (!in_array($loginAs, $allowedLoginTypes, true)) {
                return response()->json([
                    'success' => false,
                    'code' => 'UNSUPPORTED_ROLE',
                    'message' => 'Jenis login tidak didukung',
                ], 400);
            }

            // Step 6: Get user data from database based on login type
            $data = null;

            try {
                if ($loginAs == 'mahasiswa') {
                    // バ. PERBAIKAN: Menggunakan mh_v_nama
                    $data = DB::table('mh_v_nama as m')
                        ->where('m.nim', $username)
                        ->select(
                            // 'm.kdperson',
                            // 'm.kdmahasiswa',
                            // 'm.kdtamasuk',
                            'm.nim',
                            'm.namalengkap'
                            // 'm.nik',
                            // 'm.tempatlahir',
                            // 'm.tanggallahir'
                        )
                        ->first();
                } else {
                    // loginAs == tendik
                    $data = DB::table('vsdm_pegawai01 as p')
                        ->where('p.nip', $username)
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
            if ($loginAs == 'mahasiswa') {
                // バ. Role mahasiswa
                $data->role = 'mahasiswa';
            } else {
                // loginAs == tendik
                $data->role = 'tendik';
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
                'username' => $data->nim ?? $data->nip ?? null,
                'role' => $data->role ?? $loginAs,
            ];

            // Add namalengkap for mahasiswa
            if ($loginAs === 'mahasiswa' && isset($data->namalengkap)) {
                $responseData['namalengkap'] = $data->namalengkap;
            }
            // Add name for tendik (prioritize namalengkap if available)
            elseif ($loginAs === 'tendik') {
                $responseData['name'] = $data->namalengkap ?? $data->nama ?? null;
                $responseData['namalengkap'] = $data->namalengkap ?? $data->nama ?? null;
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
            $stripTags = static fn($value) => is_string($value) ? strip_tags($value) : $value;
            $token = JWTAuth::parseToken();
            $payload = $token->getPayload()->toArray();

            $loginAs = $payload['login_as'] ?? $payload['loginas'] ?? null;
            $username = $payload['username'] ?? null;

            $profile = null;
            $yudisiumInfo = null;
            $yudisiumMeta = null;

            if ($loginAs === 'mahasiswa' && $username) {
                // Ambil profil mahasiswa detail dari view
                $profile = DB::table('mh_v_nama as m')
                    ->where('m.nim', $username)
                    ->select(
                        'm.nim',
                        'm.namalengkap',
                        'm.tempatlahir',
                        'm.tanggallahir',
                        'm.prodi',
                        'm.kdprodi',
                        'm.kdjenjang',
                        'm.kdunitkerjaprodi',
                        'm.kdunitkerjafakultas',
                        'm.ipk',
                        'm.judulkaryatulis',
                        'm.judulkaryatulisinggris'
                    )
                    ->first();

                // Cari info yudisium (gelar, tanggal kelulusan, pejabat) berdasarkan kdyudisium dari nilai terbaik
                $kdyudisiumRow = DB::table('ak_v_nilaikrs_terbaik')
                    ->where('nim', $username)
                    ->select('kdyudisium')
                    ->orderByDesc('kdyudisium')
                    ->first();

                $kdyudisium = $kdyudisiumRow->kdyudisium ?? null;

                if ($kdyudisium) {
                    $yudisiumInfo = DB::table('ak_v_infoyudisium')
                        ->where('kdyudisium', $kdyudisium)
                        ->first();

                    $yudisiumMeta = DB::table('ak_v_yudisium')
                        ->where('kdyudisium', $kdyudisium)
                        ->first();
                }
            }

            // Normalisasi output untuk frontend
            $normalized = [
                'name' => $profile->namalengkap ?? $payload['name'] ?? $payload['namalengkap'] ?? null,
                'nim' => $profile->nim ?? $payload['username'] ?? null,
                'birth_place' => $profile->tempatlahir ?? null,
                'birth_date' => $profile->tanggallahir ?? null,
                'study_program' => $yudisiumInfo->namaprodi ?? $profile->prodi ?? null,
                'study_program_en' => $yudisiumInfo->namaprodiinggris ?? null,
                'degree' => $yudisiumInfo->gelar ?? null,
                'graduation_date' => $yudisiumInfo->tglkelulusan ?? $yudisiumMeta->tgllulus ?? null,
                'graduation_date_en' => $yudisiumInfo->tglkelulusaninggris ?? null,
                'education_level' => $yudisiumInfo->jenjang ?? null,
                'education_level_en' => $yudisiumInfo->jenjanginggris ?? null,
                'accreditation_detail' => $yudisiumInfo->detailakreditasi ?? null,
                'accreditation_detail_en' => $yudisiumInfo->detailakreditasiinggris ?? null,
                'gpa' => $profile->ipk ?? null,
                'thesis_title' => $stripTags($profile->judulkaryatulis ?? null),
                'thesis_title_en' => $stripTags($profile->judulkaryatulisinggris ?? null),
                'degree_en' => $yudisiumInfo->gelaringgris ?? null,
                'transcript_place_date' => $yudisiumInfo->tmptgltranskrip ?? null,
                'transcript_place_date_en' => $yudisiumInfo->tmptgltranskripinggris ?? null,
                'transcript_signer1_title' => $yudisiumInfo->jabatanttd1transkrip ?? null,
                'transcript_signer1_title_en' => $yudisiumInfo->jabatanttd1transkripinggris ?? null,
                'transcript_signer1_name' => $yudisiumInfo->namattd1transkrip ?? null,
                'certificate_signer2_title' => $yudisiumInfo->jabatanttd2ijazah ?? null,
                'certificate_signer2_title_en' => $yudisiumInfo->jabatanttd2ijazahinggris ?? null,
                'certificate_signer2_name' => $yudisiumInfo->namattd2ijazah ?? null,
                'meta' => [
                    'login_as' => $loginAs,
                    'kdprodi' => $profile->kdprodi ?? null,
                    'kdjenjang' => $profile->kdjenjang ?? null,
                    'kdunitkerjaprodi' => $profile->kdunitkerjaprodi ?? null,
                    'kdunitkerjafakultas' => $profile->kdunitkerjafakultas ?? null,
                    'kdyudisium' => $kdyudisium ?? null,
                ],
            ];

            return response()->json([
                'success' => true,
                'data' => $normalized,
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


