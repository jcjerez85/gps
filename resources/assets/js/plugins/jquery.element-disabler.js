(function ($) {
    $.fn.disabler = function() {
        var triggers = [];

        if (this.length > 0) {
            $.each($(this), function(index, _this) {
                var isTrigger = $(_this).is('[data-disabler]');
                var options = (isTrigger ? $(_this).data('disabler') : $(_this).data('disablable')).split(';');

                if (options.length > 0) {
                    var settings = {
                        selector: options[0],
                        action: options[1],
                    };
                    var currTrigger = getCurrentTrigger(isTrigger, _this, settings);

                    setTriggerOption(options, settings);
                    setTriggerTargets(triggers, currTrigger, settings);
                }
            });
        }

        $.each(triggers, function(index, value) {
            addListener(value.trigger, value.targets);
        });

        return this;
    };

    function setTriggerOption(options, settings) {
        if (2 in options) {
            if (options[2].includes('@attr:')) {
                settings.triggerAttribute = options[2].replace('@attr:', '');
            } else {
                settings.triggerValue = options[2];
            }
        }
    }

    function getCurrentTrigger(isTrigger, element, settings) {
        var currentTrigger;

        if (isTrigger) {
            currentTrigger = $(element);
        } else {
            currentTrigger = $(settings.selector);
            $.extend(settings, { selector: element });
        }

        return currentTrigger;
    }

    function setTriggerTargets(triggers, currentTrigger, settings) {

        var i = false;

        $.each(triggers, function(index, value) {
            if (value.trigger.is(currentTrigger)) {
                i = index;
                return false;
            }
        });

        if (i === false) {
            triggers.push({
                trigger: currentTrigger,
                targets: [settings]
            });
        } else {
            triggers[i].targets.push(settings);
        }
    }

    function addListener(trigger, targets) {
        var eventType = 'click.disabler';

        if (isInput(trigger, true)) {
            eventType = 'change.disabler';
        }

        $(trigger).off(eventType).on(eventType, {trigger: trigger, targets: targets}, function(e) {
            var trigger = e.data.trigger;
            var triggerTargets = e.data.targets;

            $.each(triggerTargets, function(index, target) {
                var action = target.action;
                var targets = $(target.selector);
                var triggerAttribute = target.triggerAttribute;
                var triggerValue = target.triggerValue;

                if (elementHasSelectOptions(trigger)) {
                    manageSelectTrigger(targets, action, trigger, triggerValue, triggerAttribute);
                } else {
                    manageTrigger(targets, trigger, action);
                }
            });
        }).trigger(eventType);
    }

    function elementHasSelectOptions(trigger) {
        return $(trigger).is('select') || $(trigger).is('input:radio');
    }

    function getSelectedOptions(trigger) {
        var selectedOptions = [];

        $(trigger).find(':selected').each(function(index, option) {
            selectedOptions.push($(option).val());
        });

        return selectedOptions;
    }

    function manageSelectTrigger(targets, action, trigger, triggerValue, triggerAttribute) {
        if (typeof triggerValue !== 'undefined' || typeof triggerAttribute !== 'undefined') {
            var selectedOptions = getSelectedOptions(trigger);

            $.each(targets, function(index, element) {
                $.each(selectedOptions, function(i, option) {
                    manageElement(
                        action,
                        element,
                        trigger,
                        hasTriggerValueOrAttribute(element, option, triggerValue, triggerAttribute)
                    );
                });
            });
        }
    }

    function manageTrigger(targets, trigger, action) {
        $.each(targets, function(index, element) {
            if ($(trigger).is('input:checkbox')) {
                manageElement(action, targets, trigger, $(trigger).prop('checked'));
            } else {
                manageElement(action, element, trigger, $(element).data('disabler-disabled'));
            }
        });
    }

    function hasTriggerValueOrAttribute(element, option, triggerValue, triggerAttribute) {
        return (typeof triggerValue === 'undefined'
                || triggerValue.length == 0
                || (triggerValue.length > 0 && triggerValue.split(' ').includes(option)))
            && (typeof triggerAttribute === 'undefined'
                || triggerAttribute.length == 0
                || (triggerAttribute.length > 0 && $(element).attr(triggerAttribute).split(' ').includes(option)));
    }

    function manageElement(action, elements, trigger, showElements) {
        $.each($(elements), function(index, element) {
            let isShow = action.includes('show');
            let isEnable = action.includes('enable');

            if (isShow || isEnable) {
                showElements = !showElements;
            }

            if (action.includes('hide') || isShow) {
                showElement(element, showElements);
            }

            if (action.includes('disable') || isEnable) {
                if (isInput(element)) {
                    disableElement(element, trigger, showElements);
                } else {
                    $.each($(element).find('input, select, textarea'), function(i, child) {
                        disableElement(child, trigger, showElements);
                    });
                }
            }
        });
    }

    function disableElement(element, trigger, showElement) {
        $(trigger).uniqueId();
        var attribute = 'disabler-disabled-' + trigger.attr('id');
        var elementDisabledData = getDisabledData($(element));

        if (!containsDisabledAttribute(attribute, elementDisabledData)) {
            $(element).data(attribute, $(element).prop('disabled') ? true : false);
        }

        if (!$(element).prop('disabled') && !showElement) {
            $(element).prop('disabled', true).data(attribute, true);
        } else if ($(element).prop('disabled') && showElement) {
            $(element).data(attribute, false);
            elementDisabledData = getDisabledData($(element));

            if (canEnable(elementDisabledData)) {
                $(element).prop('disabled', false);
            }
        }
    }

    function showElement(element, showElement) {
        if (showElement) {
            $(element).show();
        } else {
            $(element).hide();
        }
    }

    function isInput(element, excludeButton) {

        excludeButton = excludeButton || false;

        return $(element).is('select')
            || $(element).is('textarea')
            || $(element).is('input' + (excludeButton ? ':not([type=button], [type=submit])' : ''));
    }

    function getDisabledData(element) {
        var result = {};

        $.each($(element).data(), function(i, v) {
            if (i.includes('disablerDisabled')) {
                result[i] = v;
            }
        });

        return result;
    }

    function convertAttributeTitle(attribute) {
        return attribute.replace(
            /-([a-z])/ig,
            function(m, c) {
                return c.toUpperCase();
            });
    }

    function containsDisabledAttribute(attribute, disabledData) {
        var attributeTitle = convertAttributeTitle(attribute);

        return $.inArray(attributeTitle, disabledData) != -1;
    }

    function canEnable(disabledData) {
        var result = true;

        $.each(disabledData, function(disablerElement, isDisabled) {
            if (isDisabled == true) {
                result = false;

                return false;
            }
        });

        return result;
    }
})(jQuery);
