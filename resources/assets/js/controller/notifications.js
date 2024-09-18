function Notifications() {
    var _this = this;

    _this.notify = function(title, message, icon)
    {
        if (Notification.permission !== "granted")
            return;

        if ( ! icon) {
            icon = '/assets/images/marker-icon-2x.png';
        }

        try {
            var notification = new Notification(title, {
                body: message,
                icon: icon,
                onclick: function (event) {
                    window.focus();
                }
            });
        } catch (e) {}
    }
}