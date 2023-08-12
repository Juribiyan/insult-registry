
const infiniteScroll = {
	setup: function() {
		let pagination = $('.pagination-buttons')
		if (!pagination)
			return false;
		this.lastPage = +(pagination._$('*:last-child')/*?*/.innerText || 0)
		this.currentPage = +($('.current-page')/*?*/.innerText || 0)
		this.moreButton = pagination._ins('afterend', 
			`<button class="load-more">Загрузить ещё</button>`, true)
		this.moreButton.addEventListener('click', this.loadNext.bind(this))
		pagination.style.display = 'none'
		this.pagination = pagination
	},
	get elements() {
		return this.pagination._$$('*')
	},
	loadNext: async function() {
		if (this.currentPage >= this.lastPage) 
			return;
		if (typeof baseURI == 'undefined') {
			console.warn('No baseURI specified')
			return;
		}

		let nextPage = this.currentPage + 1

    let html = await fetchData(`${baseURI}&page=${nextPage}&ajax=1`)
    if (!html) return;

		$('.entry-list').insertAdjacentHTML('beforeend', html)

		this.elements[this.currentPage].classList/*?*/.remove('current-page')
		this.currentPage = nextPage
		if (this.currentPage == this.lastPage)
			this.moreButton.remove()
		this.elements[nextPage].outerHTML = `<span class="current-page page-link">${nextPage}</span>`
	},
}

const captcha = {
  init: function(/*forceEnable=false*/) {
    this.cw = $('.captchawrap')
    this.input = $('input[name=captcha]')
    if (!this.cw || !this.input) {
      this.disabled = true
      return
    };
    this.captchaImage = this.cw/*?*/._$('.captchaimage')
    
    injector.inject('captcha-rotting',
    `.cw-running .rotting-indicator {
      -webkit-animation-duration: ${captchaTimeout}s!important;
              animation-duration: ${captchaTimeout}s!important;
    }
    .cw-running .captchaimage,
    .cw-running .rotten-msg {
      -webkit-animation-delay: ${captchaTimeout}s!important;
              animation-delay: ${captchaTimeout}s!important;}`)
    // add image load listener
    this.captchaImage.onload = this.onImageLoad.bind(this)
    ;['animationend', 'webkitAnimationEnd', 'msAnimationEnd'].forEach(evType => {
      this.captchaImage.addEventListener(evType, this.onAnimationEnd.bind(this))
    })
    // Init form
    this.cw.onclick = this.onClick.bind(this)
    ;['click', 'focus'].forEach(evt => {
      this.input.addEventListener(evt, this.onFieldClick.bind(this))
    })
  },
  _state: 'init',
  get state() {
    return this._state
  },
  set state(s) { // Capthca state machine
    if (this.disabled) return;
    if (s == 'init') {
      this.cw.classList.remove('cw-running')
      this.cw.classList.add('cw-initial')
      this.clear()
    }
    if (s == 'init' || s == 'load') {
      this.cw.classList.add('captchaimage-invisible')
    }
    if (s == 'load') {
      this.cw.classList.remove('cw-initial', 'cw-running')
      void this.cw.offsetWidth //trigger a reflow
      this.cw.classList.add('cw-running', 'cw-loading')
    }
    else {
      this.cw.classList.remove('cw-loading')
    }
    if (s == 'run') {
      this.cw.classList.remove('captchaimage-invisible')
    }
    if (s !== 'run')
      this.clear()
    this._state = s
  },
  onClick: function(ev) {
    this.refresh()
    this.input.focus()
  },
  onFieldClick: function() {
    if (this.state == 'init' || this.state == 'expired')
      this.refresh()
  },
  onImageLoad: function() {
    if (this.state == 'load')
      this.state = 'run'
  },
  onAnimationEnd: function() {
    this.state = 'expired'
  },
  clear: function() {
    this.input.value = ''
  },
  refresh: function() {
    this.captchaImage.src = `/captcha.php?${Math.random()}`
    this.state = 'load'
  }
}

const injector = {
  inject: function(alias, css, position="beforeend") {
  var id = `injector:${alias}`
  var existing = document.getElementById(id)
  if(existing) {
    existing.innerHTML = css
    return
  }
  var head = document.head || document.getElementsByTagName('head')[0]
  head.insertAdjacentHTML(position, `<style type="text/css" id="${id}">${css}</style>`)
  },
  remove: function(alias) {
  var id = `injector:${alias}`
  var style = document.getElementById(id)
  if (style) {
    var head = document.head || document.getElementsByTagName('head')[0]
    if(head)
      head.removeChild(document.getElementById(id))
    }
  }
}

const pops = {
  init: function() {
    this.container = document.body._ins('beforeend', 
      '<div id="popup-container"></div>', true)
  },
  message: function(type, msg) {
    this.clear()
    let msgBox = this.container._ins('beforeend',`
      <div class="popup popup-${type} popup-pre-inserted">
        <h4>${this.typeMap[type]}</h4>
        ${msg}
      </div>`, true)
    window.requestAnimationFrame(() => msgBox.classList.remove('popup-pre-inserted'))
    msgBox.addEventListener('click', () => this.close(msgBox))
  },
  error: function(msg) {
    this.message('error', msg)
    return false
  },
  typeMap: {
    error: "Ошибка!"
  },
  clear: function() {
    let all = this.container._$$('.popup')
    all.forEach(p => this.close(p))
  },
  close: function(popup) {
    popup.classList.add('popup-exiting')
    setTimeout(() => popup.remove(), this.fadeOut)
  },
  fadeOut: 500
}

const Ajax = {
  init: function() {
    let form = $('form')
    if (!form) return;
    let fn = form/*?*/.dataset/*?*/.ajaxfn
    if (!fn || !this[fn]) return;
    form.addEventListener('submit', async (ev) => {
      ev.preventDefault()
      await this[fn](new FormData(form))
    })
    if ($('.comments')) {
      window.addEventListener('focus', () => this.loadComments(true))
      this.loadComments()
    }
  },
  submitEntry: async function(formData) {
    // pops.clear()
    let res = await fetchData(`/api.php?action=new&ajax=1`, {
      method: 'POST',
      body: formData
    })
    if (!res) return; // General error must be handled by fetchData()
    if (res.entry_id)
      document.location = `/entry/${res.entry_id}`
  },
  makeComment: async function(formData) {
    pops.clear()
    let res = await fetchData(`/api.php?action=new_comment&ajax=1`, {
      method: 'POST',
      body: formData
    })
    captcha.state = 'init'
    if (!res) { // General error must be handled by fetchData()
      this.busyMakingComment = false
      return
    }
    await this.loadComments(true)
    let myComment = $(`#comment_${res.id}`)
    if (myComment) // sometimes it's just not there; this is to be debugged... some day
      myComment/*?*/.classList.add('just-posted')
    $('textarea').value = ''
  },
  loadComments: async function(force = false) {
    if (force)
      clearTimeout(this.loadCommentsTimeout)
    else if (this.busyLoadingComments)
      return;
    this.busyLoadingComments = true
    // console.log('proceeding loading comments')
    let entry_id = $('input[name=entry_id]').value
    let lastComment = $('.comment:last-child')
    let after = lastComment ? lastComment.dataset.id : 0
    let comments = await fetchData(`/api.php?action=get_comments&ajax=1&entry_id=${entry_id}&after=${after}`)
    if (comments) {
      let cc = $('.comments')
      let lastComment = cc._$('.comment:last-child')
      let lastID = lastComment ? lastComment.dataset.id : 0
      let dom = this.createDOMfragment(comments)
      ;[].filter.call(dom.querySelectorAll('.comment'), c => c.dataset.id > lastID)
      .forEach(c => cc.insertAdjacentElement('beforeend', c))
    }
    this.busyLoadingComments = false
    this.loadCommentsTimeout = setTimeout(this.loadComments.bind(this), 
      document.hasFocus()
      ? this.loadCommentsRate
      : this.loadCommentRateIdle )
  },
  createDOMfragment: function(html) {
    return Range.prototype.createContextualFragment.bind(document.createRange())(html)
  },
  loadCommentsRate: 1000, //ms
  loadCommentRateIdle: 5000
}

async function fetchData(url, options={}) {
  options.credentials = 'same-origin'
  let res
  try {
    res = await fetch(url, options) 
  }
  catch(e) {
    return pops.error('Ошибка AJAX-запроса');
  }
  if (!res) return pops.error('Ошибка AJAX-запроса');
  let cType = res.headers.get("Content-Type")
  let dataType = (cType && ~cType.indexOf('json')) ? 'json' : 'text'
  let data = await res[dataType]()
  if (!res.ok || data.error) {
    return pops.error(data.error ? data.error : 'Ошибка AJAX-запроса')
  }
  return data
}

// Shorthands aka jQuery for the poor
const $ = sel => document.querySelector(sel)
const $$ = (sel, context=document) => Array.from(context.querySelectorAll(sel))
window.Element.prototype._$ = function(sel) { // barbaric yet effective
  return this.querySelector(sel)
}
window.Element.prototype._$$ = function(sel) {
  return $$(sel, this)
}
window.Element.prototype._ins = function(position, html, returnInserted=false) {
  this.insertAdjacentHTML(position, html)
  if (!returnInserted) return;
  position = position.toLowerCase()
  if (position == 'afterbegin')
    return this.firstElementChild
  else if (position == 'beforeend')
    return this.lastElementChild
  else if (position == 'beforebegin')
    return this.previousElementSibling
  else
    return this.nextElementSibling
}

;[Element.prototype, Text.prototype].forEach(e => {
  e.matches || (e.matches=e.matchesSelector || function(selector) {
    let matches = $$(selector)
    return Array.prototype.some.call(matches, e => e === this)
  })
  e.findParent = function(selector) {
    let node = this
    while (node && !node.matches(selector)) {
      node = node.parentNode
      if (! node.matches) return null
    }
    return node
  }
})

document.addEventListener('keydown', ev => {
  if (ev.key == 'Enter' && ev.ctrlKey && document.activeElement.nodeName == 'TEXTAREA') {
    let form = document.activeElement.findParent('form')
    if (form) {
      form._$('button[type=submit]').click()
    }
  }
})

function main() {
	infiniteScroll.setup()

	captcha.init()

  pops.init()

  Ajax.init()

	console.log("%c Консоль закрой, ишак", "font-weight: bold; font-size: 50px;color: red; text-shadow: 3px 3px 0 rgb(217,31,38) , 6px 6px 0 rgb(226,91,14) , 9px 9px 0 rgb(245,221,8) , 12px 12px 0 rgb(5,148,68) , 15px 15px 0 rgb(2,135,206) , 18px 18px 0 rgb(4,77,145) , 21px 21px 0 rgb(42,21,113); margin-bottom: 12px; padding: 5%");
}


