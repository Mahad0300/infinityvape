/**
 * New Vape - Edit Blog Post Page JavaScript
 * Handles live SEO preview, char counters, and featured image preview.
 */
document.addEventListener('DOMContentLoaded', () => {
  const titleInput = document.getElementById('blogTitleInput');
  const slugInput = document.getElementById('blogSlugInput');
  const seoTitleInput = document.getElementById('seoTitleInput');
  const seoDescInput = document.getElementById('seoDescInput');
  const excerptInput = document.getElementById('excerptInput');
  const previewTitle = document.getElementById('previewTitle');
  const previewDesc = document.getElementById('previewDesc');
  const previewSlug = document.getElementById('previewSlug');
  const seoTitleCount = document.getElementById('seoTitleCount');
  const seoDescCount = document.getElementById('seoDescCount');
  const imgInput = document.getElementById('featuredImageInput');
  const imgPreview = document.getElementById('featuredImagePreview');

  if (slugInput && previewSlug) {
    slugInput.addEventListener('input', () => {
      previewSlug.textContent = slugInput.value || 'post-handle';
    });
  }

  if (seoTitleInput) {
    seoTitleInput.addEventListener('input', () => {
      if (seoTitleCount) seoTitleCount.textContent = `${seoTitleInput.value.length} / 70`;
      if (previewTitle) previewTitle.textContent = seoTitleInput.value || (titleInput ? titleInput.value : '') || 'Article Title';
    });
  }

  if (seoDescInput) {
    seoDescInput.addEventListener('input', () => {
      if (seoDescCount) seoDescCount.textContent = `${seoDescInput.value.length} / 320`;
      if (previewDesc) previewDesc.textContent = seoDescInput.value || (excerptInput ? excerptInput.value : '') || 'Write a concise meta description...';
    });
  }

  if (excerptInput) {
    excerptInput.addEventListener('input', () => {
      if (seoDescInput && !seoDescInput.value && previewDesc) {
        previewDesc.textContent = excerptInput.value || 'Write a concise meta description...';
      }
    });
  }

  if (imgInput && imgPreview) {
    const defaultSrc = imgPreview.getAttribute('data-default-src') || imgPreview.src;
    imgInput.addEventListener('change', () => {
      const file = imgInput.files && imgInput.files[0];
      if (file) {
        const reader = new FileReader();
        reader.onload = (e) => {
          imgPreview.src = e.target.result;
        };
        reader.readAsDataURL(file);
      } else {
        imgPreview.src = defaultSrc;
      }
    });
  }
});
