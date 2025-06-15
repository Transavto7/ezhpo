const path = require('path')

module.exports = (env, argv) => {
  return {
    resolve: {
      extensions: ['.js', '.ts', '.vue'],
      alias: {
        '@': path.resolve(__dirname, 'resources/js'),
      },
    },
  }
}
