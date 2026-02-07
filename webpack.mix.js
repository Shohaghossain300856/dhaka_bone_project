const mix = require('laravel-mix');
const path = require('path');

mix.js('resources/js/app.js', 'public/js')
   .vue({ version: 3 })
   .postCss('resources/css/app.css', 'public/css');

mix.webpackConfig({
  resolve: {
    alias: {
      'vue$': path.resolve(__dirname, 'node_modules/vue/dist/vue.esm-bundler.js'),
      'vue':  path.resolve(__dirname, 'node_modules/vue/dist/vue.esm-bundler.js'),
    },
    modules: [
      'node_modules',
      path.resolve(__dirname, 'node_modules')
    ],
    // ✅ Webpack 5-এ '*' সমস্যা এড়াতে ক্লিন লিস্ট
    extensions: ['.js', '.jsx', '.vue', '.json', '.mjs', '.wasm'],
  },
  module: {
    rules: [
      { test: /\.vue$/, loader: 'vue-loader' }
    ]
  }
});

if (mix.inProduction()) {
  mix.version();
}
