const http = require('http')
const fs = require('fs')
const path = require('path')
const { URL } = require('url')

const root = path.resolve(__dirname, 'dist')
const backend = { hostname: '127.0.0.1', port: 8000 }
const port = 5173

const mimeTypes = {
  '.html': 'text/html',
  '.js': 'application/javascript',
  '.css': 'text/css',
  '.svg': 'image/svg+xml',
  '.png': 'image/png',
  '.jpg': 'image/jpeg',
  '.jpeg': 'image/jpeg',
  '.webp': 'image/webp',
  '.mp4': 'video/mp4',
  '.ico': 'image/x-icon',
}

const proxyToBackend = (req, res) => {
  const targetUrl = new URL(`http://${backend.hostname}:${backend.port}${req.url}`)
  const proxyRequest = http.request({
    hostname: backend.hostname,
    port: backend.port,
    path: `${targetUrl.pathname}${targetUrl.search}`,
    method: req.method,
    headers: { ...req.headers, host: `${backend.hostname}:${backend.port}` },
  }, (proxyResponse) => {
    res.writeHead(proxyResponse.statusCode || 502, proxyResponse.headers)
    proxyResponse.pipe(res)
  })

  proxyRequest.on('error', (error) => {
    res.statusCode = 502
    res.end(`Backend unavailable: ${error.message}`)
  })

  req.pipe(proxyRequest)
}

const server = http.createServer((req, res) => {
  if (/^\/(api|media|storage)(\/|$)/.test(req.url)) {
    proxyToBackend(req, res)
    return
  }

  let urlPath = req.url.split('?')[0]
  try {
    urlPath = decodeURIComponent(urlPath)
  } catch {
    res.statusCode = 400
    res.end('Invalid URL')
    return
  }
  if (urlPath === '/') urlPath = '/index.html'

  const filePath = path.resolve(root, `.${urlPath}`)
  if (!filePath.startsWith(root + path.sep)) {
    res.statusCode = 404
    res.end('Not found')
    return
  }

  fs.readFile(filePath, (err, data) => {
    if (err) {
      fs.readFile(path.join(root, 'index.html'), (fallbackError, fallbackData) => {
        if (fallbackError) {
          res.statusCode = 404
          res.end('Not found')
          return
        }
        res.setHeader('Content-Type', 'text/html')
        res.end(fallbackData)
      })
      return
    }

    res.setHeader('Content-Type', mimeTypes[path.extname(filePath)] || 'application/octet-stream')
    res.end(data)
  })
})

server.listen(port, '127.0.0.1', () => {
  console.log(`http://127.0.0.1:${port}`)
})
