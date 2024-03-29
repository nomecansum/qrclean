"use strict";
/**
 * dd-droppable.ts 5.1.1
 * Copyright (c) 2021-2022 Alain Dumesny - see GridStack root license
 */
var __extends = (this && this.__extends) || (function () {
    var extendStatics = function (d, b) {
        extendStatics = Object.setPrototypeOf ||
            ({ __proto__: [] } instanceof Array && function (d, b) { d.__proto__ = b; }) ||
            function (d, b) { for (var p in b) if (b.hasOwnProperty(p)) d[p] = b[p]; };
        return extendStatics(d, b);
    };
    return function (d, b) {
        extendStatics(d, b);
        function __() { this.constructor = d; }
        d.prototype = b === null ? Object.create(b) : (__.prototype = b.prototype, new __());
    };
})();
var __assign = (this && this.__assign) || function () {
    __assign = Object.assign || function(t) {
        for (var s, i = 1, n = arguments.length; i < n; i++) {
            s = arguments[i];
            for (var p in s) if (Object.prototype.hasOwnProperty.call(s, p))
                t[p] = s[p];
        }
        return t;
    };
    return __assign.apply(this, arguments);
};
Object.defineProperty(exports, "__esModule", { value: true });
exports.DDDroppable = void 0;
var dd_manager_1 = require("./dd-manager");
var dd_base_impl_1 = require("./dd-base-impl");
var dd_utils_1 = require("./dd-utils");
// TEST let count = 0;
var DDDroppable = /** @class */ (function (_super) {
    __extends(DDDroppable, _super);
    function DDDroppable(el, opts) {
        if (opts === void 0) { opts = {}; }
        var _this = _super.call(this) || this;
        _this.el = el;
        _this.option = opts;
        // create var event binding so we can easily remove and still look like TS methods (unlike anonymous functions)
        _this._dragEnter = _this._dragEnter.bind(_this);
        _this._dragOver = _this._dragOver.bind(_this);
        _this._dragLeave = _this._dragLeave.bind(_this);
        _this._drop = _this._drop.bind(_this);
        _this.el.classList.add('ui-droppable');
        _this.el.addEventListener('dragenter', _this._dragEnter);
        _this._setupAccept();
        return _this;
    }
    DDDroppable.prototype.on = function (event, callback) {
        _super.prototype.on.call(this, event, callback);
    };
    DDDroppable.prototype.off = function (event) {
        _super.prototype.off.call(this, event);
    };
    DDDroppable.prototype.enable = function () {
        if (!this.disabled)
            return;
        _super.prototype.enable.call(this);
        this.el.classList.remove('ui-droppable-disabled');
        this.el.addEventListener('dragenter', this._dragEnter);
    };
    DDDroppable.prototype.disable = function (forDestroy) {
        if (forDestroy === void 0) { forDestroy = false; }
        if (this.disabled)
            return;
        _super.prototype.disable.call(this);
        if (!forDestroy)
            this.el.classList.add('ui-droppable-disabled');
        this.el.removeEventListener('dragenter', this._dragEnter);
    };
    DDDroppable.prototype.destroy = function () {
        this._removeLeaveCallbacks();
        this.disable(true);
        this.el.classList.remove('ui-droppable');
        this.el.classList.remove('ui-droppable-disabled');
        _super.prototype.destroy.call(this);
    };
    DDDroppable.prototype.updateOption = function (opts) {
        var _this = this;
        Object.keys(opts).forEach(function (key) { return _this.option[key] = opts[key]; });
        this._setupAccept();
        return this;
    };
    /** @internal called when the cursor enters our area - prepare for a possible drop and track leaving */
    DDDroppable.prototype._dragEnter = function (event) {
        // TEST console.log(`${count++} Enter ${(this.el as GridHTMLElement).gridstack.opts.id}`);
        if (!this._canDrop())
            return;
        event.preventDefault();
        event.stopPropagation();
        // ignore multiple 'dragenter' as we go over existing items
        if (this.moving)
            return;
        this.moving = true;
        var ev = dd_utils_1.DDUtils.initEvent(event, { target: this.el, type: 'dropover' });
        if (this.option.over) {
            this.option.over(ev, this._ui(dd_manager_1.DDManager.dragElement));
        }
        this.triggerEvent('dropover', ev);
        this.el.addEventListener('dragover', this._dragOver);
        this.el.addEventListener('drop', this._drop);
        this.el.addEventListener('dragleave', this._dragLeave);
        // Update: removed that as it causes nested grids to no receive dragenter events when parent drags and sets this for #992. not seeing cursor flicker (chrome).
        // this.el.classList.add('ui-droppable-over');
        // make sure when we enter this, that the last one gets a leave to correctly cleanup as we don't always do
        if (DDDroppable.lastActive && DDDroppable.lastActive !== this) {
            DDDroppable.lastActive._dragLeave(event, true);
        }
        DDDroppable.lastActive = this;
    };
    /** @internal called when an moving to drop item is being dragged over - do nothing but eat the event */
    DDDroppable.prototype._dragOver = function (event) {
        event.preventDefault();
        event.stopPropagation();
    };
    /** @internal called when the item is leaving our area, stop tracking if we had moving item */
    DDDroppable.prototype._dragLeave = function (event, forceLeave) {
        var _a;
        // TEST console.log(`${count++} Leave ${(this.el as GridHTMLElement).gridstack.opts.id}`);
        event.preventDefault();
        event.stopPropagation();
        // ignore leave events on our children (we get them when starting to drag our items)
        // but exclude nested grids since we would still be leaving ourself, 
        // but don't handle leave if we're dragging a nested grid around
        if (!forceLeave) {
            var onChild = dd_utils_1.DDUtils.inside(event, this.el);
            var drag = dd_manager_1.DDManager.dragElement.el;
            if (onChild && !((_a = drag.gridstackNode) === null || _a === void 0 ? void 0 : _a.subGrid)) { // dragging a nested grid ?
                var nestedEl = this.el.gridstack.engine.nodes.filter(function (n) { return n.subGrid; }).map(function (n) { return n.subGrid.el; });
                onChild = !nestedEl.some(function (el) { return dd_utils_1.DDUtils.inside(event, el); });
            }
            if (onChild)
                return;
        }
        if (this.moving) {
            var ev = dd_utils_1.DDUtils.initEvent(event, { target: this.el, type: 'dropout' });
            if (this.option.out) {
                this.option.out(ev, this._ui(dd_manager_1.DDManager.dragElement));
            }
            this.triggerEvent('dropout', ev);
        }
        this._removeLeaveCallbacks();
        if (DDDroppable.lastActive === this) {
            delete DDDroppable.lastActive;
        }
    };
    /** @internal item is being dropped on us - call the client drop event */
    DDDroppable.prototype._drop = function (event) {
        if (!this.moving)
            return; // should not have received event...
        event.preventDefault();
        var ev = dd_utils_1.DDUtils.initEvent(event, { target: this.el, type: 'drop' });
        if (this.option.drop) {
            this.option.drop(ev, this._ui(dd_manager_1.DDManager.dragElement));
        }
        this.triggerEvent('drop', ev);
        this._removeLeaveCallbacks();
    };
    /** @internal called to remove callbacks when leaving or dropping */
    DDDroppable.prototype._removeLeaveCallbacks = function () {
        if (!this.moving) {
            return;
        }
        delete this.moving;
        this.el.removeEventListener('dragover', this._dragOver);
        this.el.removeEventListener('drop', this._drop);
        this.el.removeEventListener('dragleave', this._dragLeave);
        // Update: removed that as it causes nested grids to no receive dragenter events when parent drags and sets this for #992. not seeing cursor flicker (chrome).
        // this.el.classList.remove('ui-droppable-over');
    };
    /** @internal */
    DDDroppable.prototype._canDrop = function () {
        return dd_manager_1.DDManager.dragElement && (!this.accept || this.accept(dd_manager_1.DDManager.dragElement.el));
    };
    /** @internal */
    DDDroppable.prototype._setupAccept = function () {
        var _this = this;
        if (this.option.accept && typeof this.option.accept === 'string') {
            this.accept = function (el) {
                return el.matches(_this.option.accept);
            };
        }
        else {
            this.accept = this.option.accept;
        }
        return this;
    };
    /** @internal */
    DDDroppable.prototype._ui = function (drag) {
        return __assign({ draggable: drag.el }, drag.ui());
    };
    return DDDroppable;
}(dd_base_impl_1.DDBaseImplement));
exports.DDDroppable = DDDroppable;
//# sourceMappingURL=dd-droppable.js.map