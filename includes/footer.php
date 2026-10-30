</main>
<footer class="bg-dark text-light mt-5 py-4">
  <div class="container">
    <div class="row">
      <div class="col-md-6">
        <p class="mb-0"><?= e(get_setting('footer_text','© Portfolio')) ?></p>
      </div>
      <div class="col-md-6 text-md-end">
        <?php foreach (['social_github'=>'github','social_linkedin'=>'linkedin','social_twitter'=>'twitter'] as $k=>$ic): ?>
          <?php if ($v = get_setting($k)): ?>
            <a class="text-light me-2" href="<?= e($v) ?>" target="_blank"><i class="bi bi-<?= $ic ?>"></i></a>
          <?php endif; ?>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
</footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?= BASE_URL ?>assets/js/app.js"></script>
</body></html>