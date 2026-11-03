/**
 * HOME PAGE JAVASCRIPT
 * Handles home page interactions, hero animations, and product showcase handlers.
 */

document.addEventListener('DOMContentLoaded', () => {
  // 1. Add To Cart Button Interactive Feedback & Cart Drawer Trigger
  const addToCartButtons = document.querySelectorAll('.btn-add-to-cart');
  addToCartButtons.forEach((btn) => {
    btn.addEventListener('click', (e) => {
      e.preventDefault();
      e.stopPropagation();

      const card = btn.closest('.product-card');
      const cartInstance = window.InfinityCart || window.NewVapeCart;
      if (card && cartInstance) {
        const titleEl = card.querySelector('.product-title a, .product-title');
        const priceEl = card.querySelector('.current-price');
        const imgEl = card.querySelector('.product-img');

        const title = titleEl ? titleEl.textContent.trim() : 'Vape Device';
        const priceStr = priceEl ? priceEl.textContent.replace(/[^0-9.]/g, '') : '19.99';
        const price = parseFloat(priceStr) || 19.99;
        const image = imgEl ? imgEl.getAttribute('src') : 'assets/images/no-image.svg';
        const id = btn.getAttribute('data-id') || title.toLowerCase().replace(/[^a-z0-9]+/g, '-');

        cartInstance.add({
          id: id,
          title: title,
          price: price,
          quantity: 1,
          flavor: '',
          image: image
        });
      }

      const originalText = btn.textContent;
      btn.textContent = 'ADDED!';
      btn.style.backgroundColor = '#10b981';
      btn.style.boxShadow = '0 4px 14px rgba(16, 185, 129, 0.4)';

      setTimeout(() => {
        btn.textContent = originalText;
        btn.style.backgroundColor = '';
        btn.style.boxShadow = '';
      }, 1500);
    });
  });

  // 3. Products Slider Arrow Navigation (Supports all showcases on page)
  const showcaseSections = document.querySelectorAll('.products-showcase-section');
  showcaseSections.forEach((section) => {
    const productsGrid = section.querySelector('.products-grid');
    const prevBtn = section.querySelector('.slider-arrow-btn.prev-btn');
    const nextBtn = section.querySelector('.slider-arrow-btn.next-btn');

    if (productsGrid && prevBtn && nextBtn) {
      const getScrollStep = () => {
        const firstCard = productsGrid.querySelector('.product-card');
        if (firstCard) {
          return firstCard.offsetWidth + 16;
        }
        return 250;
      };

      nextBtn.addEventListener('click', (e) => {
        e.preventDefault();
        const step = getScrollStep();
        const maxScroll = productsGrid.scrollWidth - productsGrid.clientWidth;

        if (productsGrid.scrollLeft >= maxScroll - 15) {
          productsGrid.scrollTo({ left: 0, behavior: 'smooth' });
        } else {
          productsGrid.scrollBy({ left: step * 2, behavior: 'smooth' });
        }
      });

      prevBtn.addEventListener('click', (e) => {
        e.preventDefault();
        const step = getScrollStep();

        if (productsGrid.scrollLeft <= 15) {
          productsGrid.scrollTo({ left: productsGrid.scrollWidth, behavior: 'smooth' });
        } else {
          productsGrid.scrollBy({ left: -step * 2, behavior: 'smooth' });
        }
      });
    }
  });



  // 4. Blog Section Slider Navigation
  const blogSection = document.querySelector('.blog-showcase-section');
  if (blogSection) {
    const blogGrid = blogSection.querySelector('.blog-cards-grid');
    const blogPrev = blogSection.querySelector('.blog-prev-btn');
    const blogNext = blogSection.querySelector('.blog-next-btn');

    if (blogGrid && blogPrev && blogNext) {
      const getBlogStep = () => {
        const firstCard = blogGrid.querySelector('.blog-card');
        if (firstCard) {
          const style = window.getComputedStyle(blogGrid);
          const gap = parseFloat(style.columnGap || style.gap || '14') || 14;
          return firstCard.offsetWidth + gap;
        }
        return blogGrid.clientWidth || 300;
      };

      blogNext.addEventListener('click', (e) => {
        e.preventDefault();
        e.stopPropagation();
        const step = getBlogStep();
        const maxScroll = blogGrid.scrollWidth - blogGrid.clientWidth;

        if (maxScroll <= 0) return;

        if (blogGrid.scrollLeft >= maxScroll - 10) {
          blogGrid.scrollTo({ left: 0, behavior: 'smooth' });
        } else {
          blogGrid.scrollBy({ left: step, behavior: 'smooth' });
        }
      });

      blogPrev.addEventListener('click', (e) => {
        e.preventDefault();
        e.stopPropagation();
        const step = getBlogStep();
        const maxScroll = blogGrid.scrollWidth - blogGrid.clientWidth;

        if (maxScroll <= 0) return;

        if (blogGrid.scrollLeft <= 10) {
          blogGrid.scrollTo({ left: maxScroll, behavior: 'smooth' });
        } else {
          blogGrid.scrollBy({ left: -step, behavior: 'smooth' });
        }
      });
    }
  }

  // 5. Dynamic Hero Banner Slider Controls & Auto-Rotation
  const heroWrapper = document.getElementById('heroSliderWrapper');
  if (heroWrapper) {
    const slides = heroWrapper.querySelectorAll('.hero-slide-item');
    const dots = heroWrapper.querySelectorAll('.hero-slider-dot');
    const prevBtn = heroWrapper.querySelector('#heroPrevBtn');
    const nextBtn = heroWrapper.querySelector('#heroNextBtn');
    let currentIndex = 0;
    let autoPlayTimer = null;

    if (slides.length > 1) {
      const showSlide = (index) => {
        if (index < 0) {
          index = slides.length - 1;
        } else if (index >= slides.length) {
          index = 0;
        }
        currentIndex = index;

        slides.forEach((slide, idx) => {
          if (idx === currentIndex) {
            slide.classList.add('active');
          } else {
            slide.classList.remove('active');
          }
        });

        dots.forEach((dot, idx) => {
          if (idx === currentIndex) {
            dot.classList.add('active');
          } else {
            dot.classList.remove('active');
          }
        });
      };

      const startAutoPlay = () => {
        stopAutoPlay();
        autoPlayTimer = setInterval(() => {
          showSlide(currentIndex + 1);
        }, 5500);
      };

      const stopAutoPlay = () => {
        if (autoPlayTimer) {
          clearInterval(autoPlayTimer);
          autoPlayTimer = null;
        }
      };

      if (nextBtn) {
        nextBtn.addEventListener('click', (e) => {
          e.preventDefault();
          showSlide(currentIndex + 1);
          startAutoPlay();
        });
      }

      if (prevBtn) {
        prevBtn.addEventListener('click', (e) => {
          e.preventDefault();
          showSlide(currentIndex - 1);
          startAutoPlay();
        });
      }

      dots.forEach((dot) => {
        dot.addEventListener('click', (e) => {
          e.preventDefault();
          const targetIndex = parseInt(dot.getAttribute('data-index'), 10);
          if (!isNaN(targetIndex)) {
            showSlide(targetIndex);
            startAutoPlay();
          }
        });
      });

      heroWrapper.addEventListener('mouseenter', stopAutoPlay);
      heroWrapper.addEventListener('mouseleave', startAutoPlay);

      // Start initial auto play
      startAutoPlay();
    }
  }
});