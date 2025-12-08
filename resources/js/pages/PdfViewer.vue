<template>
  <div class="pdf-viewer">
    <div class="pdf-toolbar">
      <VBtn
        icon
        small
        @click="zoomOut"
      >
        <VIcon>mdi-minus</VIcon>
      </VBtn>
      <span class="zoom-text">{{ Math.round(scale * 100) }}%</span>
      <VBtn
        icon
        small
        @click="zoomIn"
      >
        <VIcon>mdi-plus</VIcon>
      </VBtn>
      <VBtn
        icon
        small
        @click="rotate"
      >
        <VIcon>mdi-rotate-right</VIcon>
      </VBtn>
      <VBtn
        icon
        small
        @click="download"
      >
        <VIcon>mdi-download</VIcon>
      </VBtn>
      <VBtn
        icon
        small
        @click="print"
      >
        <VIcon>mdi-printer</VIcon>
      </VBtn>
    </div>
    <div
      ref="container"
      class="pdf-container"
    >
      <canvas ref="pdfCanvas" />
    </div>
  </div>
</template>

<script>
import * as pdfjsLib from 'pdfjs-dist'
import { ref, onMounted, watch } from 'vue'

// Sesuaikan path worker sesuai dengan struktur project
pdfjsLib.GlobalWorkerOptions.workerSrc = '/js/pdf.worker.js'

export default {
  name: 'PdfViewer',
  props: {
    pdfData: {
      type: String,
      required: true,
    },
  },
  setup(props) {
    const container = ref(null)
    const pdfCanvas = ref(null)
    const scale = ref(1)
    const rotation = ref(0)
    let pdfDoc = null
    let currentPage = 1

    const renderPage = async () => {
      if (!pdfDoc) return

      const page = await pdfDoc.getPage(currentPage)
      const viewport = page.getViewport({ scale: scale.value, rotation: rotation.value })
      
      const canvas = pdfCanvas.value
      const context = canvas.getContext('2d')
      
      canvas.height = viewport.height
      canvas.width = viewport.width

      const renderContext = {
        canvasContext: context,
        viewport: viewport,
      }

      try {
        await page.render(renderContext).promise
      } catch (error) {
        console.error('Error rendering PDF:', error)
      }
    }

    const loadPDF = async () => {
      try {
        pdfDoc = await pdfjsLib.getDocument(props.pdfData).promise
        renderPage()
      } catch (error) {
        console.error('Error loading PDF:', error)
      }
    }

    const zoomIn = () => {
      scale.value = Math.min(scale.value + 0.1, 3)
      renderPage()
    }

    const zoomOut = () => {
      scale.value = Math.max(scale.value - 0.1, 0.5)
      renderPage()
    }

    const rotate = () => {
      rotation.value = (rotation.value + 90) % 360
      renderPage()
    }

    const download = () => {
      const link = document.createElement('a')

      link.href = props.pdfData
      link.download = 'document.pdf'
      link.click()
    }

    const print = () => {
      const iframe = document.createElement('iframe')

      iframe.style.display = 'none'
      iframe.src = props.pdfData
      document.body.appendChild(iframe)
      iframe.contentWindow.print()
    }

    watch(() => props.pdfData, () => {
      loadPDF()
    })

    onMounted(() => {
      loadPDF()
    })

    return {
      container,
      pdfCanvas,
      scale,
      zoomIn,
      zoomOut,
      rotate,
      download,
      print,
    }
  },
}
</script>

<style scoped>
.pdf-viewer {
  display: flex;
  flex-direction: column;
  height: 100%;
  width: 100%;
}

.pdf-toolbar {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 8px;
  background: #f5f5f5;
  border-radius: 4px;
  margin-bottom: 8px;
}

.zoom-text {
  min-width: 60px;
  text-align: center;
}

.pdf-container {
  flex: 1;
  overflow: auto;
  border: 1px solid #e0e0e0;
  border-radius: 4px;
  background: #fff;
  display: flex;
  justify-content: center;
  align-items: flex-start;
  padding: 16px;
}

canvas {
  max-width: 100%;
  height: auto;
  box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}
</style>