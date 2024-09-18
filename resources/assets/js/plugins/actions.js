let actions = {
    stack: [],
    add: function (action) {
        this.stack.push(action);
    },
    clear: function (filter = {}) {
        this.stack.forEach(function(item, index, object) {
            if (this._isActionMatchingFilter(item, filter)) {
                object.splice(index, 1);
            }
        }, this);
    },
    execute: function (filter, args = null) {
        this.stack.forEach(function(item, index, object) {
            if (!this._isActionMatchingFilter(item, filter)) {
                return;
            }

            item.execute(args);

            if (item.isExpired()) {
                object.splice(index, 1);
            }
        }, this);
    },
    _isActionMatchingFilter: function (action, filter) {
        for (let key in filter) {
            if (action.filter.hasOwnProperty(key)) {
                let actionValues = Array.isArray(action.filter[key]) ? action.filter[key] : [action.filter[key]];
                let filterValues = Array.isArray(filter[key]) ? filter[key] : [filter[key]];

                let intersection = actionValues.filter(value => filterValues.includes(value));

                if (intersection.length === 0) {
                    return false;
                }
            }
        }

        return true;
    },
}

class Action {
    constructor(closure, filter = {}, lifetime = 1) {
        this.filter = filter;
        this.closure = closure;
        this.lifetime = lifetime;
    }

    execute(args = null) {
        if (Number.isInteger(this.lifetime)) {
            this.lifetime--;
        }

        return this.closure(args);
    }

    isExpired() {
        return this.lifetime < 1 && this.lifetime !== null;
    }
}