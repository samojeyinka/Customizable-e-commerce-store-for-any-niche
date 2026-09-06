(function(){
'use strict';
var KEY='glorefy_favourites';
var CFG=window.GLORY_CART_CONFIG||{};
var DOMAIN=CFG.domain||'';
var AUTH=+CFG.auth||0;

var state={ids:[]};

function lsRead(){try{return JSON.parse(localStorage.getItem(KEY))||[]}catch(e){return[]}}
function lsWrite(a){try{localStorage.setItem(KEY,JSON.stringify(a))}catch(e){}}

function has(pid){
  pid=Number(pid);
  return state.ids.indexOf(pid)!==-1;
}

function postJSON(url,body){return fetch(url,{method:'POST',headers:{'Content-Type':'application/json','Accept':'application/json'},body:JSON.stringify(body||{})}).then(function(r){if(!r.ok)throw new Error('HTTP '+r.status);return r.json();});}

function paintHeart(el,on){
  if(!el)return;
  if(on){
    el.classList.add('favorite-active','fa-solid','text-[#C2185B]');
    el.classList.remove('fa-regular','text-white');
  }else{
    el.classList.remove('favorite-active','fa-solid','text-[#C2185B]');
    el.classList.add('fa-regular','text-white');
  }
}

function stroke(el,on){
  if(!el)return;
  if(on){
    el.classList.add('favorite-active','text-[#C2185B]');
    el.classList.remove('text-[#262626]','fa-regular');
    el.classList.add('fa-solid');
  }else{
    el.classList.remove('favorite-active','text-[#C2185B]');
    el.classList.add('text-[#262626]','fa-regular');
    el.classList.remove('fa-solid');
  }
}

function showFavToast(msg,type){
  if(window.GlorifyToast&&GlorifyToast.show){GlorifyToast.show(msg,type);return;}
  var t=document.getElementById('favorites-toast');
  if(t){t.textContent=msg;t.classList.remove('hidden');setTimeout(function(){t.classList.add('hidden');},2500);return;}
  var to=document.createElement('div');
  to.id='notification-toast';
  to.className='fixed bottom-4 right-4 px-4 py-2 rounded-md shadow-lg z-50 transition-opacity duration-300 '+(type==='warning'?'bg-orange-500':'bg-green-600')+';color:#fff;';
  to.textContent=msg;
  document.body.appendChild(to);
  setTimeout(function(){to.style.opacity='0';},2500);
  setTimeout(function(){if(to.parentNode)to.parentNode.removeChild(to);},2900);
}

function syncFavServe(){
  if(!AUTH)return;
  var raw=lsRead();
  if(!raw.length)return;
  var fd=new FormData();
  fd.append('items',JSON.stringify(raw));
  fetch(DOMAIN+'/products/sync-favourites.php',{method:'POST',body:fd}).then(function(r){return r.json();}).then(function(d){
    if(d.success)lsWrite([]);
  }).catch(function(){});
}

function syncHearts(){
  document.querySelectorAll('.add-to-favourite').forEach(function(el){
    var p=el.getAttribute('data-product-id');
    if(p)paintHeart(el,has(p));
  });
  var sh=document.getElementById('product-fav-heart');
  if(sh){
    var p=sh.getAttribute('data-product-id');
    if(p)stroke(sh,has(p));
  }
  document.querySelectorAll('.fav-heart').forEach(function(el){
    var p=el.getAttribute('data-product-id');
    if(p)paintHeart(el,has(p));
  });
}

function syncAll(){
  if(!AUTH){
    state.ids=lsRead().map(function(p){return Number(p);});
    syncHearts();
  }else{
    postJSON(DOMAIN+'/products/fetch-favourites.php',{}).then(function(d){
      state.ids=(d.ids||[]).map(Number);
      syncHearts();
    }).catch(function(){});
  }
}

function setState(ids){
  state.ids=ids.slice().map(Number);
  syncHearts();
}

function toggle(pid,heartEl){
  pid=Number(pid);
  if(!pid)return;
  if(AUTH){
    var isOn=heartEl?heartEl.classList.contains('favorite-active'):has(pid);
    var fd=new FormData();
    fd.append('product_id',pid);
    if(isOn)fd.append('action','remove');
    fetch(DOMAIN+'/products/toggle-favorite.php',{method:'POST',body:fd}).then(function(r){return r.json();}).then(function(d){
      if(d.success){
        if(d.action==='added'||d.action==='exists'){
          if(state.ids.indexOf(pid)===-1)state.ids.push(pid);
          showFavToast('Added to Favourites','success');
        }else if(d.action==='removed'){
          state.ids=state.ids.filter(function(x){return x!==pid;});
          showFavToast('Removed from Favourites','warning');
        }
        if(heartEl){
          if(heartEl.id==='product-fav-heart')stroke(heartEl,has(pid));
          else paintHeart(heartEl,has(pid));
        }
      }else if(d.redirect){
        window.location.href=d.redirect;
      }else{
        showFavToast(d.message||'Error updating favourites','error');
      }
    }).catch(function(){showFavToast('Network error','error');});
  }else{
    var a=lsRead().map(Number);
    var idx=a.indexOf(pid);
    var nowOn;
    if(heartEl){
      nowOn=!heartEl.classList.contains('favorite-active');
    }else{
      nowOn=!has(pid);
    }
    if(nowOn){if(idx===-1)a.push(pid);showFavToast('Added to Favourites','success');}
    else{a.splice(idx,1);showFavToast('Removed from Favourites','warning');}
    lsWrite(a);
    state.ids=a;
    if(heartEl){
      if(heartEl.id==='product-fav-heart')stroke(heartEl,has(pid));
      else paintHeart(heartEl,has(pid));
    }
  }
}

function init(){
  syncAll();
  document.addEventListener('click',function(e){
    var ic=e.target.closest('.add-to-favourite');
    if(ic){
      e.preventDefault();e.stopPropagation();
      var p=ic.getAttribute('data-product-id');
      if(p)toggle(p,ic);
      return;
    }
    var sh=e.target.closest('#product-fav-heart');
    if(sh){
      e.preventDefault();
      var p2=sh.getAttribute('data-product-id');
      if(p2)toggle(p2,sh);
      return;
    }
  });
  if(AUTH)syncFavServe();
}

if(document.readyState==='loading')document.addEventListener('DOMContentLoaded',init);else init();

window.GlorifyFav={toggle:toggle,has:has,setState:setState,syncAll:syncAll};
})();