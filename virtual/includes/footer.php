  </div><!-- /content -->
</div><!-- /main -->
</div><!-- /layout -->
<?php foreach($extra_js as $js): ?>
<script src="<?=$js?>"></script>
<?php endforeach; ?>
<script>
// Close sidebar on outside click (mobile)
document.addEventListener('click',function(e){
  const sb=document.getElementById('sidebar');
  if(sb && sb.classList.contains('open') && !sb.contains(e.target)){
    sb.classList.remove('open');
  }
});
</script>
</body>
</html>
