(function(){
'use strict';
var KEY='glorefy_cart';
var CFG=window.GLORY_CART_CONFIG||{};
var DOMAIN=CFG.domain||'';
var AUTH=+CFG.auth||0;

var state={items:[],count:0,subtotal:0,drawerOpen:false};
var drawerHideTimer=null;

function lsRead(){try{return JSON.parse(localStorage.getItem(KEY))||[]}catch(e){return[]}}
function lsWrite(a){try{localStorage.setItem(KEY,JSON.stringify(a))}catch(e){}}
function fmt(n){return '\u20A6'+Number(n||0).toLocaleString()}
function esc(s){return String(s==null?'':s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');}

function ensureToast(){
  var t=document.getElementById('cart-toast');
  if(!t){t=document.createElement('div');t.id='cart-toast';t.className='hidden fixed bottom-4 right-4 bg-green-600 text-white py-2 px-4 rounded-md shadow-lg z-[900]';document.body.appendChild(t);}
  return t;
}
function showToast(msg,color){
  var t=ensureToast();
  t.className='fixed bottom-4 right-4 py-2 px-4 rounded-md shadow-lg z-[900] text-white '+(color||'bg-green-600');
  t.textContent=msg;
  t.classList.remove('hidden');
  clearTimeout(drawerHideTimer||0);
  drawerHideTimer=setTimeout(function(){t.classList.add('hidden');},3000);
}

function badge(){
  var c=document.getElementById('cart-count');
  var b=document.getElementById('cart-badge');
  if(c)c.textContent=state.count;
  if(b){state.count>0?b.classList.remove('hidden'):b.classList.add('hidden');}
}

function lk(pid,vid){return String(pid)+'_'+String(vid||'');}
function lsIndex(a,pid,vid){var k=lk(pid,vid);for(var i=0;i<a.length;i++){if(lk(a[i].product_id,a[i].variant_id)===k)return i;}return -1;}
function has(pid,vid){var k=lk(pid,vid);for(var i=0;i<state.items.length;i++){if(lk(state.items[i].product_id,state.items[i].variant_id)===k)return true;}return false;}

function postJSON(url,body){return fetch(url,{method:'POST',headers:{'Content-Type':'application/json','Accept':'application/json'},body:JSON.stringify(body||{})}).then(function(r){if(!r.ok)throw new Error('HTTP '+r.status);return r.json();});}

function syncCardButtons(){
  document.querySelectorAll('.add-to-cart-btn').forEach(function(btn){
    var p=btn.getAttribute('data-product-id');
    var y=has(p);
    btn.textContent=y?'Added to Cart':'Add to Cart';
    btn.setAttribute('data-in-cart',y?'true':'false');
  });
  document.querySelectorAll('.cart-toggle-icon').forEach(function(ic){
    var p=ic.getAttribute('data-product-id');
    var y=has(p);
    if(y){ic.classList.add('in-cart','fa-circle-check','glor-text');ic.classList.remove('fa-cart-plus','text-white');}
    else{ic.classList.remove('in-cart','fa-circle-check','glor-text');ic.classList.add('fa-cart-plus','text-white');}
  });
  document.querySelectorAll('.cart-toggle-button').forEach(function(btn){
    var p=btn.getAttribute('data-product-id');
    var y=has(p);
    btn.textContent=y?'Added to Cart':'Add to Cart';
    btn.setAttribute('data-in-cart',y?'true':'false');
    if(y){btn.classList.add('glor-bg','text-white');}else{btn.classList.remove('glor-bg','text-white');}
  });
}

function detailPid(){
  var el=document.getElementById('product-id');
  if(el&&el.value)return el.value;
  return null;
}
function detailVid(){
  var el=document.getElementById('product-size');
  if(el)return el.value||null;
  return null;
}
function syncDetailBtn(){
  var btn=document.getElementById('add-to-cart-btn');
  if(!btn)return;
  var pid=detailPid();
  var vid=detailVid();
  var y=has(pid,vid);
  if(y){
    btn.textContent='Added to Cart \u2713';
    btn.classList.add('bg-[#E1F5E6]');
    btn.classList.remove('bg-[#E8E9F2]');
    btn.style.borderColor='#4CAF50';
  }else{
    btn.textContent='Add to Cart';
    btn.classList.remove('bg-[#E1F5E6]');
    btn.classList.add('bg-[#E8E9F2]');
    btn.style.borderColor='#969AC4';
  }
}

function syncGuest(){
  if(AUTH)return;
  var a=lsRead();
  state.items=a;
  state.count=0;
  for(var i=0;i<a.length;i++)state.count+=Number(a[i].quantity)||0;
  badge();
  syncCardButtons();
}

function renderDrawer(){
  var body=document.getElementById('cart-drawer-items');
  var countEl=document.getElementById('cart-drawer-count');
  var subEl=document.getElementById('cart-drawer-subtotal');
  var footer=document.getElementById('cart-drawer-footer');
  var empty=document.getElementById('cart-empty');
  if(countEl)countEl.textContent='('+state.count+')';
  if(subEl)subEl.textContent=fmt(state.subtotal);
  if(state.items.length===0){
    if(footer)footer.classList.add('hidden');
    if(body)body.innerHTML='<div id="cart-empty" class="flex-1 flex flex-col items-center justify-center p-6 text-center"><i class="fa-solid fa-cart-shopping text-[48px] glor-text opacity-30 mb-3"></i><p class="text-[15px] text-[#262626] font-medium mb-1">Your cart is empty</p><p class="text-[13px] text-[#777777]">Add items to get started</p></div>';
    return;
  }
  if(footer)footer.classList.remove('hidden');
  if(!body)return;
  var html='';
  for(var i=0;i<state.items.length;i++){
    var it=state.items[i];
    var img=/^https?:\/\//i.test(it.image||'')?it.image:DOMAIN+'/assets/products/'+(it.image||'default.svg');
    var url=it.slug?DOMAIN+'/products/show.php?slug='+encodeURIComponent(it.slug):DOMAIN+'/products/show.php?id='+it.product_id;
    var meta=[];
    if(it.size)meta.push(it.size);
    if(it.texture)meta.push(it.texture);
    var k=it.product_id+'_'+(it.variant_id||'');
    html+='<div class="flex gap-3 p-3 border-b border-[#E1E1E1]" data-cart-row="'+k+'">';
    html+='<img src="'+esc(img)+'" class="w-16 h-16 object-cover rounded-[4px] shrink-0" alt="">';
    html+='<div class="flex-1 min-w-0">';
    html+='<a href="'+esc(url)+'" class="block truncate text-[13px] font-medium text-[#262626] hover:glor-text">'+esc(it.product_name)+'</a>';
    if(meta.length)html+='<div class="text-[12px] text-[#8A8A8A] mt-0.5">'+esc(meta.join(' \u00B7 '))+'</div>';
    html+='<div class="flex items-center justify-between mt-2">';
    html+='<div class="flex items-center border border-[#E1E1E1] rounded-md">';
    html+='<button type="button" class="cart-qty-btn px-2 py-1 text-[13px] text-[#262626]" data-action="minus" data-pid="'+it.product_id+'" data-vid="'+(it.variant_id||'')+'">-</button>';
    html+='<span class="cart-qty-val w-8 text-center text-[13px] text-[#262626]">'+it.quantity+'</span>';
    html+='<button type="button" class="cart-qty-btn px-2 py-1 text-[13px] text-[#262626]" data-action="plus" data-pid="'+it.product_id+'" data-vid="'+(it.variant_id||'')+'">+</button>';
    html+='</div>';
    html+='<div class="flex items-center gap-3">';
    html+='<span class="text-[13px] text-[#262626] font-medium">'+fmt(it.line_total)+'</span>';
    html+='<button type="button" class="cart-item-remove glor-text text-[12px] font-medium" data-pid="'+it.product_id+'" data-vid="'+(it.variant_id||'')+'">Remove</button>';
    html+='</div></div></div></div>';
  }
  body.innerHTML=html;
}

function loadDrawer(){
  if(AUTH){
    postJSON(DOMAIN+'/products/cart-fetch.php',{}).then(function(d){
      state.items=d.items||[];
      state.count=d.count||0;
      state.subtotal=d.subtotal||0;
      badge();
      renderDrawer();
    }).catch(function(){renderDrawer();});
  }else{
    var raw=lsRead();
    if(raw.length===0){state.items=[];state.count=0;state.subtotal=0;renderDrawer();return;}
    postJSON(DOMAIN+'/products/cart-fetch.php',{items:raw}).then(function(d){
      state.items=d.items||[];
      state.count=d.count||0;
      state.subtotal=d.subtotal||0;
      badge();
      renderDrawer();
    }).catch(function(){renderDrawer();});
  }
}

function openDrawer(){
  var overlay=document.getElementById('cart-overlay');
  var drawer=document.getElementById('cart-drawer');
  if(!overlay||!drawer)return;
  state.drawerOpen=true;
  overlay.classList.remove('hidden');
  overlay.classList.add('flex');
  drawer.classList.remove('translate-x-full');
  drawer.classList.add('translate-x-0');
  document.body.style.overflow='hidden';
  loadDrawer();
}
function closeDrawer(){
  var overlay=document.getElementById('cart-overlay');
  var drawer=document.getElementById('cart-drawer');
  if(!overlay||!drawer)return;
  state.drawerOpen=false;
  overlay.classList.add('hidden');
  overlay.classList.remove('flex');
  drawer.classList.add('translate-x-full');
  drawer.classList.remove('translate-x-0');
  document.body.style.overflow='';
}

function add(pid,vid,qty,opts){
  opts=opts||{};
  qty=qty||1;
  vid=(vid===''||vid===undefined)?null:vid;
  if(AUTH){
    var fd=new FormData();
    fd.append('product_id',pid);
    if(vid!==null)fd.append('variant_id',vid);
    fd.append('quantity',qty);
    fetch(DOMAIN+'/products/add-to-cart.php',{method:'POST',body:fd}).then(function(r){return r.json();}).then(function(d){
      if(d.success){
        state.count=d.cart_count||state.count;
        badge();syncCardButtons();syncDetailBtn();
        showToast(opts.toast||'Added to cart');
        if(opts.onSuccess)opts.onSuccess(d);
      }else{
        if(d.redirect)window.location.href=d.redirect;
        else showToast(d.message||'Could not add to cart','bg-red-600');
      }
    }).catch(function(){showToast('Network error','bg-red-600');});
  }else{
    var a=lsRead();
    var idx=lsIndex(a,pid,vid);
    if(idx>=0){a[idx].quantity=(Number(a[idx].quantity)||1)+Number(qty);}
    else{a.push({product_id:Number(pid),variant_id:vid!==null?Number(vid):null,quantity:Number(qty)});}
    lsWrite(a);
    syncGuest();
    syncDetailBtn();
    showToast(opts.toast||'Added to cart');
    if(opts.onSuccess)opts.onSuccess({mode:'guest'});
  }
}
function remove(pid,vid){
  vid=(vid===''||vid===undefined||vid===null)?null:vid;
  if(AUTH){
    var fd=new FormData();
    fd.append('product_id',pid);
    if(vid!==null)fd.append('variant_id',vid);
    fd.append('action','remove');
    fetch(DOMAIN+'/products/cart-update.php',{method:'POST',body:fd}).then(function(r){return r.json();}).then(function(d){
      state.count=d.cart_count||state.count;
      badge();syncCardButtons();syncDetailBtn();
      if(state.drawerOpen)loadDrawer();
    }).catch(function(){});
  }else{
    var a=lsRead();
    var idx=lsIndex(a,pid,vid);
    if(idx>=0){a.splice(idx,1);lsWrite(a);syncGuest();}
    syncDetailBtn();
    if(state.drawerOpen)loadDrawer();
  }
}
function setQty(pid,vid,qty){
  vid=(vid===''||vid===undefined||vid===null)?null:vid;
  qty=Math.max(0,Number(qty)||0);
  if(AUTH){
    var fd=new FormData();
    fd.append('product_id',pid);
    if(vid!==null)fd.append('variant_id',vid);
    fd.append('quantity',qty);
    fd.append('action','set');
    fetch(DOMAIN+'/products/cart-update.php',{method:'POST',body:fd}).then(function(r){return r.json();}).then(function(d){
      state.count=d.cart_count||state.count;
      badge();syncCardButtons();loadDrawer();
    }).catch(function(){});
  }else{
    var a=lsRead();
    if(qty<=0){var idx=lsIndex(a,pid,vid);if(idx>=0)a.splice(idx,1);}
    else{var idx=lsIndex(a,pid,vid);if(idx>=0)a[idx].quantity=qty;else a.push({product_id:Number(pid),variant_id:vid!==null?Number(vid):null,quantity:qty});}
    lsWrite(a);syncGuest();loadDrawer();
  }
}

function initLoad(){
  if(AUTH){
    postJSON(DOMAIN+'/products/cart-fetch.php',{}).then(function(d){
      state.items=d.items||[];
      state.count=d.count||0;
      state.subtotal=d.subtotal||0;
      badge();syncCardButtons();
      var raw=lsRead();
      if(raw.length>0)postJSON(DOMAIN+'/products/cart-sync.php',{items:raw}).then(function(){lsWrite([]);}).catch(function(){});
    }).catch(function(){});
  }else{
    syncGuest();
  }
}

function init(){
  document.addEventListener('click',function(e){
    if(e.target.closest('[data-open-cart]')){e.preventDefault();openDrawer();return;}
    if(e.target.closest('[data-close-cart]')){e.preventDefault();closeDrawer();return;}
    if(e.target.id==='cart-overlay'){closeDrawer();return;}
    var rm=e.target.closest('.cart-item-remove');
    if(rm){remove(rm.getAttribute('data-pid'),rm.getAttribute('data-vid'));return;}
    var qt=e.target.closest('.cart-qty-btn');
    if(qt){
      var row=qt.closest('[data-cart-row]');
      var cur=row?Number(row.querySelector('.cart-qty-val').textContent):1;
      var next=qt.getAttribute('data-action')==='plus'?cur+1:Math.max(1,cur-1);
      setQty(qt.getAttribute('data-pid'),qt.getAttribute('data-vid')||null,next);
      return;
    }
    var ic=e.target.closest('.cart-toggle-icon');
    if(ic){e.preventDefault();e.stopPropagation();var pid=ic.getAttribute('data-product-id');if(has(pid))remove(pid);else add(pid,null,1);return;}
    var btn=e.target.closest('.add-to-cart-btn');
    if(btn){e.preventDefault();e.stopPropagation();var pid=btn.getAttribute('data-product-id');if(has(pid))remove(pid);else add(pid,null,1);return;}
    var btnB=e.target.closest('.cart-toggle-button');
    if(btnB){e.preventDefault();e.stopPropagation();var pid=btnB.getAttribute('data-product-id');if(has(pid))remove(pid);else add(pid,null,1);return;}
  });
  document.addEventListener('keydown',function(e){if(e.key==='Escape'&&state.drawerOpen)closeDrawer();});
  document.addEventListener('change',function(e){
    if(e.target&&e.target.id==='product-size')syncDetailBtn();
  });
  initLoad();
}

function guestCartRenderer(){
  var hasPage=document.getElementById('cart-guest-table-body')||document.getElementById('cart-guest-mobile');
  if(!hasPage||AUTH)return;
  var raw=lsRead();
  if(raw.length===0){
    var emptyEl=document.getElementById('cart-empty');
    if(emptyEl)emptyEl.classList.remove('hidden');
    return;
  }
  postJSON(DOMAIN+'/products/cart-fetch.php',{items:raw}).then(function(d){
      state.items=d.items||[];
      state.count=d.count||0;
      state.subtotal=d.subtotal||0;
      badge();
      var tb=document.getElementById('cart-guest-table-body');
      var mb=document.getElementById('cart-guest-mobile');
      var emptyEl=document.getElementById('cart-empty');
      if(emptyEl)emptyEl.classList.add('hidden');
      state.items.forEach(function(it){
        var img=/^https?:\/\//i.test(it.image||'')?it.image:DOMAIN+'/assets/products/'+(it.image||'default.svg');
        var url=it.slug?DOMAIN+'/products/show.php?slug='+encodeURIComponent(it.slug):DOMAIN+'/products/show.php?id='+it.product_id;
        var meta=[];
        if(it.size)meta.push('Size: '+it.size);
        if(it.variant_id)meta.push('Variant: '+it.variant_id);
        var metaHtml=meta.map(function(m){return '<p class="text-[#262626] text-[13px] md:text-[14px] font-[\'Open Sans\']">'+m+'</p>';}).join('');
        if(tb){
          var tr=document.createElement('tr');
          tr.innerHTML='<td class="py-3 flex gap-2"><div class="w-[131.64px] h-[88.73px] rounded-[4px] overflow-hidden"><img src="'+esc(img)+'" class="h-full w-full object-cover"></div><div class="flex flex-col gap-[2px]"><a href="'+esc(url)+'" class="text-[#262626] text-[13px] md:text-[14px] font-[\'Open Sans\'] hover:glor-text">'+esc(it.product_name)+'</a>'+metaHtml+'</div></td><td class="text-[#262626] text-[15px] md:text-[16px] font-[\'Open Sans\']">'+fmt(it.line_total)+'</td><td></td><td><span class="py-2 px-4 bg-gray-200 text-gray-600 text-[16px] font-[\'Open Sans\'] rounded-[28px]">N/A</span></td><td><button class="text-sm text-red-600 hover:text-red-800 guest-remove" data-pid="'+it.product_id+'" data-vid="'+(it.variant_id||'')+'">Remove</button></td>';
          tb.appendChild(tr);
        }
        if(mb){
          var d=document.createElement('div');
          d.className='border-[1px] border-[#E1E1E1] rounded-[8px] p-2 flex justify-between';
          d.innerHTML='<div class="flex gap-2"><div class="w-[80px] h-[80px] rounded-[4px] overflow-hidden"><img src="'+esc(img)+'" class="h-full w-full object-cover"></div><div class="flex flex-col gap-[2px]"><a href="'+esc(url)+'" class="text-[#262626] text-[13px] md:text-[14px] font-[\'Open Sans\'] hover:glor-text">'+esc(it.product_name)+'</a>'+metaHtml+'<button class="text-sm text-red-600 hover:text-red-800 guest-remove" data-pid="'+it.product_id+'" data-vid="'+(it.variant_id||'')+'">Remove</button></div></div><div><p class="text-[#262626] text-[15px] md:text-[16px] font-[\'Open Sans\']">'+fmt(it.line_total)+'</p></div>';
          mb.appendChild(d);
        }
      });
    }).catch(function(){});
  document.addEventListener('click',function(e){
    var rm=e.target.closest('.guest-remove');
    if(rm){remove(rm.getAttribute('data-pid'),rm.getAttribute('data-vid'));}
  });
}

if(document.readyState==='loading'){document.addEventListener('DOMContentLoaded',function(){init();guestCartRenderer();});}else{init();guestCartRenderer();}

window.GlorifyCart={add:add,remove:remove,setQty:setQty,open:openDrawer,close:closeDrawer,get:function(){return state;},has:has,syncBadge:badge,syncDetailBtn:syncDetailBtn,setDetailPid:function(pid){var el=document.getElementById('product-id');if(!el){el=document.createElement('input');el.id='product-id';el.type='hidden';document.body.appendChild(el);}el.value=pid;}};
})();