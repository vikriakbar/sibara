</section><div class="footer">© <?=date('Y')?> SIBARA · Made by Vikri Akbar - SMK Negeri 2 Bangkalan</div></main></div><script>document.querySelectorAll('[data-confirm]').forEach(x=>x.addEventListener('click',e=>{if(!confirm(x.dataset.confirm))e.preventDefault()}));</script><script>
(function(){
 const root=document.documentElement;
 let saved='light';
 try{saved=localStorage.getItem('sibara-theme')||'light'}catch(e){}
 root.dataset.theme=saved;
 document.querySelectorAll('[data-theme-toggle]').forEach(btn=>btn.addEventListener('click',()=>{
   const next=root.dataset.theme==='dark'?'light':'dark';root.dataset.theme=next;
   try{localStorage.setItem('sibara-theme',next)}catch(e){}
   btn.setAttribute('aria-label',next==='dark'?'Aktifkan mode terang':'Aktifkan mode gelap');
 }));
})();
</script></body></html>
