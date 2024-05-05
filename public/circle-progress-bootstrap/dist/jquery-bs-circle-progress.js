(function ($) {
    $.fn.circleProgress = function (options, params) {
        if ($(this).length > 1) {
            return $(this).each(function (i, e) {
                return $(e).circleProgress(options, params);
            })
        }

        const DEFAULTS = {
            size: 200,
            value: 0,
            background: 'transparent',
            color: 'primary',
            progressWidth: null
        };

        const $element = $(this);

        function setValue(value) {
            let showValue = value;
            if (value > 100) {
                value = 100;
            }
            $element.data('value', showValue);
            $element.find('.js-prozess-value').text(Math.round(showValue));
            const left = $element.find('.progress-left .progress-bar');
            const right = $element.find('.progress-right .progress-bar');
            if (value > 0) {
                if (value <= 50) {
                    right.css('transform', 'rotate(' + percentageToDegrees(value) + 'deg)')
                } else {
                    right.css('transform', 'rotate(180deg)')
                    left.css('transform', 'rotate(' + percentageToDegrees(value - 50) + 'deg)')
                }
            } else {
                left.css('transform', 'rotate(0deg)');
                right.css('transform', 'rotate(0deg)');
            }
        }

        function getColor(color) {
            if (['dark', 'secondary', 'light', 'info', 'warning', 'danger', 'success', 'primary'].includes(color)) {
                return {
                    class: 'progress-bar border-' + color
                }
            } else {
                return {
                    class: 'progress-bar',
                    css: {
                        borderColor: color
                    }
                }
            }
        }

        function getColorBackgroundColor(color) {
            if (['dark', 'secondary', 'light', 'info', 'warning', 'danger', 'success', 'primary', 'transparent'].includes(color)) {
                return {
                    class: 'bg-' + color
                }
            } else {
                return {
                    css: {
                        background: color
                    }
                }
            }
        }

        function build($e) {
            const settings = $e.data('settings');
            const spanColor = getColor(settings.color);
            const borderWidth = settings.progressWidth ?? (settings.size / 10);
            const backgroundColor = getColorBackgroundColor(settings.background);
            const backgroundCss = $.extend({
                maxWidth: settings.size + 'px',
                minWidth: settings.size + 'px',
                width: settings.size + 'px',
                maxHeight: settings.size + 'px',
                minHeight: settings.size + 'px',
                height: settings.size + 'px',
                borderRadius: settings.size + 'px'
            }, backgroundColor.css || {})
            $e
                .addClass('position-relative')
                .css(backgroundCss);

            if (backgroundColor.class) {
                $e.addClass(backgroundColor.class)
            }

            const cssSpan = {
                width: "50%",
                height: "100%",
                overflow: 'hidden',
                position: 'absolute',
                top: 0,
                zIndex: 1
            };
            const progressBarCss = {
                width: "100%",
                height: "100%",
                background: "none",
                borderWidth: borderWidth + "px",
                borderStyle: "solid",
                position: "absolute",
                top: 0,
                zIndex: 2
            };

            // left border
            const leftCSS = $.extend(structuredClone(cssSpan), {left: 0});
            const leftProgressBarCss = $.extend(structuredClone(cssSpan), structuredClone(progressBarCss), {
                left: "100%",
                borderTopRightRadius: (settings.size / 2 + borderWidth) + "px",
                borderBottomRightRadius: (settings.size / 2 + borderWidth) + "px",
                borderLeft: 0,
                transformOrigin: "center left"
            });

            const left = $('<span>', {
                class: 'progress-left',
                css: leftCSS
            }).appendTo($e);

            $('<span>', {
                class: spanColor.class,
                css: spanColor.css ? $.extend(leftProgressBarCss, spanColor.css) : leftProgressBarCss
            }).appendTo(left);

            // right border
            const rightCSS = $.extend(structuredClone(cssSpan), {right: 0});
            const rightProgressBarCss = $.extend(structuredClone(cssSpan), structuredClone(progressBarCss), {
                left: "-100%",
                borderTopLeftRadius: (settings.size / 2 + borderWidth) + "px",
                borderBottomLeftRadius: (settings.size / 2 + borderWidth) + "px",
                borderRight: 0,
                transformOrigin: "center right"
            });

            const right = $('<span>', {
                class: 'progress-right',
                css: rightCSS
            }).appendTo($e);


            $('<span>', {
                class: spanColor.class,
                css: spanColor.css ? $.extend(rightProgressBarCss, spanColor.css) : rightProgressBarCss
            }).appendTo(right);

            // value container
            $('<div>', {
                css: {
                    position: 'absolute',
                    top: 0,
                    left: 0,
                    width: '50%',
                    height: '100%',
                    overflow: 'hidden',
                    zIndex: 0,
                    fontSize: (settings.size / 5) + 'px',
                    borderWidth: borderWidth + "px",
                    borderStyle: 'solid',
                    borderColor: 'rgb(128, 128, 128, .3)'
                },
                class: 'progress-value w-100 h-100 rounded-circle d-flex align-items-center justify-content-center',
                html: '<div class=" font-weight-bold m-0"><span class="js-prozess-value"></span><sup class="small">%</sup></div>'
            }).appendTo($e);


            setValue($element.data('value') || settings.value);
        }

        function percentageToDegrees(percentage) {

            return percentage / 100 * 360

        }

        function events($e) {

        }

        function init() {
            if (!$element.data('init')) {

                if (typeof options === 'object' && !$element.data('settings')) {
                    $element.data('settings', $.extend(DEFAULTS, options || {}));
                }

                if (!$element.data('settings')) {
                    $element.data('settings', DEFAULTS);
                }

                build($element);
                events($element);
                $element.data('init', true);
            }
        }

        init();

        if (typeof options === 'string') {
            switch (options) {
                case 'val': {
                    setValue(params);
                    break;
                }
                case 'updateOptions': {
                    const beforeOptions = $element.data('settings') || DEFAULTS;
                    const beforeValue = $element.data('value');
                    const newOptions = $.extend(beforeOptions, params);
                    $element.data('settings', newOptions);
                    $element.empty();
                    build($element);
                    if (params.value) {
                        setValue(params.value)
                    }
                    break;
                }

            }
        }

        return $element;

    }


}(jQuery));