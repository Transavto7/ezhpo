const path = require('path')

module.exports = (env, argv) => {
  return {
    resolve: {
      extensions: ['.js', '.vue'],
      alias: {
        '@': path.resolve(__dirname, 'resources/js'),
      },
    },
  }
}