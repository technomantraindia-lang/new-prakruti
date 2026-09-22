const http = require('http')
const fs = require('fs')
const path = require('path')

const root = path.resolve(__dirname, 'dist')
const port = 4174

const mimeTypes = {
  '.html': 'text/html',
  '.js': 'application/javascript',
  '.css': 'text/css',
  '.svg': 'image/svg+xml',
  '.png': 'image/png',
  '.mp4': 'video/mp4',
  '.ico': 'image/x-icon',
}

const server = http.createServer((req, res) => {
  let urlPath = req.url.split('?')[0]
  if (urlPath === '/') urlPath = '/index.html'

  const filePath = path.join(root, urlPath)

  fs.readFile(filePath, (err, data) => {
    if (err) {
      res.statusCode = 404
      res.end('Not found')
      return
    }

    res.setHeader('Content-Type', mimeTypes[path.extname(filePath)] || 'application/octet-stream')
    res.end(data)
  })
})

server.listen(port, '127.0.0.1', () => {
  console.log(`http://127.0.0.1:${port}`)
})
