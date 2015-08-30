/*
 * This file is part of the Asset Injection package, an RunOpenCode project.
 *
 * (c) 2015 RunOpenCode
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
(function(window, undefined){

    var
        RunOpenCode = RunOpenCode || {},

        loadedAssets = {
            javascripts: [],
            stylesheets: []
        };

    RunOpenCode.DynamicAssetInclusion = {};

    RunOpenCode.DynamicAssetInclusion.prototype.loadJavascript = function(url) {
        if (!loadedAssets.javascripts[url]) {

            loadedAssets.javascripts[url] = url;

            var
                elem = document.createElement('script');

            elem.setAttribute('type', 'text/javascript');
            elem.setAttribute('src', url);

        };
    };

    RunOpenCode.DynamicAssetInclusion.prototype.loadStylesheet = function(url, media) {
        if (!loadedAssets.stylesheets[url]) {

            loadedAssets.stylesheets[url] = url;

            var
                elem = document.createElement('link');

            elem.setAttribute('rel', 'stylesheet');
            elem.setAttribute('type', 'text/css');

            if (media && media.trim() != '') {
                elem.setAttribute('media', media);
            };

            elem.setAttribute('href', url);
        };
    };

    if (!window.RunOpenCode) { // Make it public!
        window.RunOpenCode = RunOpenCode;
    };

})(window);