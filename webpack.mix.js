const mix = require('laravel-mix');
const MomentLocalesPlugin = require('moment-locales-webpack-plugin');
const { CleanWebpackPlugin } = require('clean-webpack-plugin');

mix.js('resources/src/main.js', 'public')
  .js('resources/src/login.js', 'public')
  .vue();

mix.webpackConfig({
  cache: false,
  output: {
    filename: 'js/[name].min.js',
    chunkFilename: 'js/bundle/[name].[hash].js',
  },
  stats: { children: true },
  plugins: [
    new MomentLocalesPlugin(),
    new CleanWebpackPlugin({
      cleanOnceBeforeBuildPatterns: ['./js/*'],
    }),
  ],
});
