const fs = require('fs')
const path = require('path')
const { execFileSync, spawn } = require('child_process')

const projectRoot = process.cwd()
const availableDrive = ['Z', 'Y', 'X', 'W', 'V'].find((letter) => {
  try {
    return !fs.existsSync(`${letter}:\\`)
  } catch {
    return false
  }
})

if (!availableDrive) {
  console.error('No temporary Windows drive is available for the nested frontend path.')
  process.exit(1)
}

const mappedRoot = `${availableDrive}:\\`

const cleanup = () => {
  try {
    execFileSync('subst', [`${availableDrive}:`, '/D'], { stdio: 'ignore' })
  } catch {
    // The drive can be cleaned up automatically when Windows logs off.
  }
}

try {
  execFileSync('subst', [`${availableDrive}:`, projectRoot], { stdio: 'ignore' })
} catch (error) {
  console.error('Could not prepare the frontend working drive:', error.message)
  process.exit(1)
}

try {
  execFileSync(process.env.ComSpec || 'cmd.exe', ['/d', '/s', '/c', 'npm.cmd run build:direct'], {
    cwd: mappedRoot,
    stdio: 'inherit',
  })
} catch {
  cleanup()
  process.exit(1)
}

const child = spawn(process.execPath, [path.join(projectRoot, 'serve-dist.cjs')], {
  cwd: projectRoot,
  stdio: 'inherit',
  windowsHide: false,
})

child.on('exit', (code, signal) => {
  cleanup()
  if (signal) {
    process.kill(process.pid, signal)
  } else {
    process.exit(code ?? 0)
  }
})

process.on('SIGINT', () => child.kill('SIGINT'))
process.on('SIGTERM', () => child.kill('SIGTERM'))
