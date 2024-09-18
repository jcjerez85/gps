function Chat() {
    var _this = this;

    _this.init = function() {
        app.socket.on('message', function(msg) {
            _this.newMessage(msg.data);
        });

        _this.updateUnreadMsgCounter()
    };

    _this.reloadChatableList = function() {
        if (!$('#chats_table:visible').length)
            return;

        tables.reload('chats_table');
    };

    _this.updateUnreadMsgCounter = function () {
        let $container = $('#unread-msg-count');

        if (!$container.length)
            return;

        $.ajax({
            type: 'GET',
            dataType: 'json',
            url: app.urls.chatUnreadMsgTotalCount,
            success: function(res) {
                if (res && res.count !== undefined) {
                    $container.html(res.count ? res.count : '');

                    if (!(app.socket && app.socket.connected)) {
                        _this.reloadChatableList();
                    }
                }
            },
            complete: function(){
                clearTimeout(app.chat.unreadMsgCounterCheck);

                app.chat.unreadMsgCounterCheck = setTimeout(function() {
                    app.chat.updateUnreadMsgCounter();
                }, app.checkChatUnreadFrequency * 1000);
            },
        });
    };

    _this.scrollBottom = function($container){
        var $messages = $container;

        if ( ! $container.hasClass('messages'))
            $messages = $('.messages', $container);

        if ( ! $messages.length)
            return;

        $messages.scrollTop( $messages[0].scrollHeight - $messages[0].clientHeight);
    };

    _this.keyDown = function(e, input){
        if (e.keyCode != 13)
            return;

        $form = $(input).closest('form');

        _this.send($form);
    };

    _this.send = function (form) {
        var $form = $(form);

        if ( ! $('input[name="message"]', $form).val() )
            return false;

        $.ajax({
            type: 'POST',
            dataType: "json",
            data: $(form).serialize(),
            url: $(form).attr('action'),
            success: function (res) {
                if (res.status == 1) {
                    _this.newMessage(res.message);
                }
            }
        });

        $('input[name="message"]', $form).val('');

        return false;
    };

    _this.isOpenedChat = function(chat_id) {
        return $('.conversation[data-chatId="'+chat_id+'"]:visible').length;
    };

    _this.loadChat = function(url, $container) {
        $.ajax({
            type: 'GET',
            dataType: 'html',
            url:  url,
            success: function(response) {
                $container.append( response );

                _this.scrollBottom($container);

                _this.updateUnreadMsgCounter();
            }
        });
    };

    _this.getMessages = function(url, $container) {
        $.ajax({
            type: 'GET',
            dataType: 'json',
            url: url,
            timeout: 60000,
            beforeSend: function() {
                loader.add($container);
            },
            success: function(response) {
                if (response.status == 1) {
                    if (response.data.length > 0) {

                        var _height = $container.prop('scrollHeight');

                        $.each(response.data, function (key, value) {
                            _this.preppendMessage(value, $container);
                        });

                        $container.scrollTop( $container.prop('scrollHeight') - _height);
                    }
                    if (response.next_page_url) {
                        $container.prepend('<li data-next="'+response.next_page_url+'"></li>');
                    }
                }
            },
            complete: function() {
                loader.remove($container);
            },
            error: function(jqXHR, textStatus, errorThrown) {}
        });
    };

    _this.openChatModal = function(url) {
        var $conversation = $('#conversation');

        $.ajax({
            type: 'GET',
            dataType: 'html',
            url:  url,
            success: function(response) {
                $conversation.html(response);

                _this.scrollBottom($conversation);

                _this.updateUnreadMsgCounter();
            },
            beforeSend: function() {
                loader.add( $conversation );
            },
            complete: function() {
                loader.remove( $conversation );
            },
        });
    };

    _this.getMessagesLatest = function(url, $container, time) {
        if (!$container.is(':visible'))
            return;

        if (app.socket && app.socket.connected)
            return;

        $.ajax({
            type: 'GET',
            dataType: 'json',
            url: url + '?time=' + time,
            success: function(response) {
                if (response.status === 1) {
                    if (response.data.length > 0) {
                        response.data.forEach(_this.newMessage);
                    }

                    time = response.timestamp;
                }

                setTimeout(function() {
                    _this.getMessagesLatest(url, $container, time);
                }, app.checkChatFrequency * 1000);
            },

        });
    };

    _this.newMessage = function(message) {
        if (_this.isOpenedChat(message.chat_id)) {
            var _conversations = $('.conversation[data-chatId="'+message.chat_id+'"]:visible .messages');

            $.each(_conversations, function(index, _conversation){
                _this.appendMessage(message, _conversation);
            });
        } else {
            _this.reloadChatableList();
            _this.loadChat(message.chat_url, $('#conversations'));
        }
    };

    _this.preppendMessage = function(message, $container) {
        $container = $( $container );

        if (!_this.isNewMessage(message, $container))
            return;

        $container.prepend(_this.messageHtml(message));
    };

    _this.appendMessage = function(message, $container) {
        $container = $( $container );

        if (!_this.isNewMessage(message, $container))
            return;

        $container.append(_this.messageHtml(message));

        _this.scrollBottom($container);
    };

    _this.isNewMessage = function(message, $container) {
        return !$container.find('#message-' + message.id).length;
    };

    _this.messageHtml = function(message) {
        var html = $('<li class="message" id="message-' + message.id + '"></li>');
        if (message.chattable_id == app.user_id)
            html.addClass('me');

        html.append( $('<span class="text">' + message.content + '</span>') );
        html.append( $('<span class="author">' + message.sender_name + '</span>') );

        return html;
    };

    _this.close = function(elem) {
        $(elem).closest('.conversation').remove();
    }
};

$("body").on("click", ".chat_device", function(e){
    e.preventDefault();
    app.chat.loadChat($(this).data('url'), $('#conversations'));
});

