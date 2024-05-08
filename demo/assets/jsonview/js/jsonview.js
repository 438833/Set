var JSONView = (function(document)
{
    var Object_prototype_toString = ({}).toString;
    var DatePrototypeAsString = Object_prototype_toString.call(new Date);
    /** @constructor */
    function JSONView()
    {
        this._dom_container = document.createElement('pre');
        this._dom_container.classList.add('jsonview');
    };
    /**
     * Визуализация объекта JSON
     * 
     * @param {Object|Array} json входное значение
     * @param {Number} [inputMaxLvl] обрабатывать только до максимального уровня, где 0..n, -1 неограниченно
     * @param {Number} [inputColAt] свернуть на уровне, где 0..n, -1 неограниченно
     */
    JSONView.prototype.showJSON = function(jsonValue, inputMaxLvl, inputColAt)
    {
        // обрабатывать только до maxLvl, где 0..n, -1 неограниченно
        var maxLvl = typeof inputMaxLvl === 'number' ? inputMaxLvl : -1; // максимальный уровень
        // Collapse at level colAt, where 0..n, -1 unlimited
        var colAt = typeof inputColAt === 'number' ? inputColAt : -1; // свернуть в
        this._dom_container.innerHTML = '';
        walkJSONTree(this._dom_container, jsonValue, maxLvl, colAt, 0);
    };
    /**
     * Получить контейнер с предварительным объектом - этот контейнер используется для визуализации данных JSON
     * 
     * @return {Element}
     */
    JSONView.prototype.getContainer = function()
    {
        return this._dom_container;
    };
    /**
     * Рекурсивное блуждание для входного значения
     * 
     * @param {Element} outputParent - это элемент, который будет содержать новый DOM
     * @param {Object|Array} value входное значение
     * @param {Number} maxLvl обрабатывать только до максимального уровня, где 0..n, -1 неограниченно
     * @param {Number} colAt свернуть на уровне, где 0..n, -1 неограниченно
     * @param {Number} lvl текущий уровень
     */
    function walkJSONTree(outputParent, value, maxLvl, colAt, lvl)
    {
        var isDate = Object_prototype_toString.call(value) === DatePrototypeAsString;
        var realValue = !isDate && typeof value === 'object' && value !== null && 'toJSON' in value ? value.toJSON() : value;
        if(typeof realValue === 'object' && realValue !== null && !isDate)
        {
            var isMaxLvl = maxLvl >= 0 && lvl >= maxLvl;
            var isCollapse = colAt >= 0 && lvl >= colAt;
            var isArray = Array.isArray(realValue);
            var items = isArray ? realValue : Object.keys(realValue);
            if(lvl === 0)
            {
                // корневой уровень
                var rootCount = _createItemsCount(items.length);
                // скрыть/показать
                var rootLink = _createLink(isArray ? '[' : '{');
                if(items.length)
                {
                    rootLink.addEventListener('click', function()
                    {
                        if(isMaxLvl) return;
                        rootLink.classList.toggle('collapsed');
                        rootCount.classList.toggle('hide');
                        // основной список
                        outputParent.querySelector('ul')
                            .classList.toggle('hide');
                    });
                    if(isCollapse)
                    {
                        rootLink.classList.add('collapsed');
                        rootCount.classList.remove('hide');
                    }
                }
                else
                {
                    rootLink.classList.add('empty');
                }
                rootLink.appendChild(rootCount);
                outputParent.appendChild(rootLink); // вывести rootLink
            }
            if(items.length && !isMaxLvl)
            {
                var len = items.length - 1;
                var ulList = document.createElement('ul');
                ulList.setAttribute('data-level', lvl);
                ulList.classList.add('type-' + (isArray ? 'array' : 'object'));
                items.forEach(function(key, ind)
                {
                    var item = isArray ? key : value[key];
                    var li = document.createElement('li');
                    if(typeof item === 'object')
                    {
                        // null && date
                        if(!item || item instanceof Date)
                        {
                            li.appendChild(document.createTextNode(isArray ? '' : key + ': '));
                            li.appendChild(createSimpleViewOf(item ? item : null, true));
                        }
                        // array & object
                        else
                        {
                            var itemIsArray = Array.isArray(item);
                            var itemLen = itemIsArray ? item.length : Object.keys(item)
                                .length;
                            // empty
                            if(!itemLen)
                            {
                                li.appendChild(document.createTextNode(key + ': ' + (itemIsArray ? '[]' : '{}')));
                            }
                            else
                            {
                                // 1+ элементы
                                var itemTitle = (typeof key === 'string' ? key + ': ' : '') + (itemIsArray ? '[' : '{');
                                var itemLink = _createLink(itemTitle);
                                var itemsCount = _createItemsCount(itemLen);
                                // maxLvl - только текст, без ссылки
                                if(maxLvl >= 0 && lvl + 1 >= maxLvl)
                                {
                                    li.appendChild(document.createTextNode(itemTitle));
                                }
                                else
                                {
                                    itemLink.appendChild(itemsCount);
                                    li.appendChild(itemLink);
                                }
                                walkJSONTree(li, item, maxLvl, colAt, lvl + 1);
                                li.appendChild(document.createTextNode(itemIsArray ? ']' : '}'));
                                var list = li.querySelector('ul');
                                var itemLinkCb = function()
                                {
                                    itemLink.classList.toggle('collapsed');
                                    itemsCount.classList.toggle('hide');
                                    list.classList.toggle('hide');
                                };
                                // скрыть/показать
                                itemLink.addEventListener('click', itemLinkCb);
                                // обвал нижнего уровня
                                if(colAt >= 0 && lvl + 1 >= colAt)
                                {
                                    itemLinkCb();
                                }
                            }
                        }
                    }
                    // simple values
                    else
                    {
                        // ключи объекта с ключом:
                        if(!isArray)
                        {
                            li.appendChild(document.createTextNode(key + ': '));
                        }
                        // recursive
                        walkJSONTree(li, item, maxLvl, colAt, lvl + 1);
                    }
                    // добавить запятую до конца
                    if(ind < len)
                    {
                        li.appendChild(document.createTextNode(','));
                    }
                    ulList.appendChild(li);
                }, this);
                outputParent.appendChild(ulList); // вывод ulList
            }
            else if(items.length && isMaxLvl)
            {
                var itemsCount = _createItemsCount(items.length);
                itemsCount.classList.remove('hide');
                outputParent.appendChild(itemsCount); // вывод itemsCount
            }
            if(lvl === 0)
            {
                // empty root
                if(!items.length)
                {
                    var itemsCount = _createItemsCount(0);
                    itemsCount.classList.remove('hide');
                    outputParent.appendChild(itemsCount); // вывод itemsCount
                }
                // корневая оболочка
                outputParent.appendChild(document.createTextNode(isArray ? ']' : '}'));
                // обвал
                if(isCollapse)
                {
                    outputParent.querySelector('ul')
                        .classList.add('hide');
                }
            }
        }
        else
        {
            // simple values
            outputParent.appendChild(createSimpleViewOf(value, isDate));
        }
    };
    /**
     * Создать простое значение (no object|array).
     * 
     * @param  {Number|String|null|undefined|Date} value входное значение
     * @return {Element}
     */
    function createSimpleViewOf(value, isDate)
    {
        var spanEl = document.createElement('span');
        var type = typeof value;
        var asText = '' + value;
        if(type === 'string')
        {
            asText = '"' + value + '"';
        }
        else if(value === null)
        {
            type = 'null';
            //asText = 'null';
        }
        else if(isDate)
        {
            type = 'date';
            asText = value.toLocaleString();
        }
        spanEl.className = 'type-' + type;
        spanEl.textContent = asText;
        return spanEl;
    };
    /**
     * создать элемент подсчета элементов
     * 
     * @param  {Number} count количество элементов
     * @return {Element}
     */
    function _createItemsCount(count)
    {
        var itemsCount = document.createElement('span');
        itemsCount.className = 'items-ph hide';
        itemsCount.innerHTML = _getItemsTitle(count);
        return itemsCount;
    };
    /**
     * Создать кликабельную ссылку
     * 
     * @param  {String} title название ссылки
     * @return {Element}
     */
    function _createLink(title)
    {
        var linkEl = document.createElement('a');
        linkEl.classList.add('list-link');
        linkEl.href = 'javascript:void(0)';
        linkEl.innerHTML = title || '';
        return linkEl;
    };
    /**
     * Получить текущее название эдемента для подсчета
     * 
     * @param  {Number} count количество элементов
     * @return {String}
     */
    function _getItemsTitle(count)
    {
        var itemsTxt = count > 1 || count === 0 ? 'items' : 'item';
        return(count + ' ' + itemsTxt);
    };
    return JSONView;
})(document);