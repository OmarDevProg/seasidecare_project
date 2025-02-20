(function ($) {

  "use strict";

  var initPreloader = function () {
    $(document).ready(function ($) {
      var Body = $('body');
      Body.addClass('preloader-site');
    });
    $(window).load(function () {
      $('.preloader-wrapper').fadeOut();
      $('body').removeClass('preloader-site');
    });
  }

  // init Chocolat light box
  var initChocolat = function () {
    Chocolat(document.querySelectorAll('.image-link'), {
      imageSize: 'contain',
      loop: true,
    })
  }

  var initSwiper = function () {

    var swiper = new Swiper(".main-swiper", {
      speed: 500,
      pagination: {
        el: ".swiper-pagination",
        clickable: true,
      },
    });

    var category_swiper = new Swiper(".category-carousel", {
      slidesPerView: 6,
      spaceBetween: 30,
      speed: 500,
      navigation: {
        nextEl: ".category-carousel-next",
        prevEl: ".category-carousel-prev",
      },
      breakpoints: {
        0: {
          slidesPerView: 2,
        },
        768: {
          slidesPerView: 3,
        },
        991: {
          slidesPerView: 4,
        },
        1500: {
          slidesPerView: 6,
        },
      }
    });

    var brand_swiper = new Swiper(".brand-carousel", {
      slidesPerView: 4,
      spaceBetween: 30,
      speed: 500,
      navigation: {
        nextEl: ".brand-carousel-next",
        prevEl: ".brand-carousel-prev",
      },
      breakpoints: {
        0: {
          slidesPerView: 2,
        },
        768: {
          slidesPerView: 2,
        },
        991: {
          slidesPerView: 3,
        },
        1500: {
          slidesPerView: 4,
        },
      }
    });

    var products_swiper = new Swiper(".products-carousel", {
      slidesPerView: 5,
      spaceBetween: 30,
      speed: 500,
      navigation: {
        nextEl: ".products-carousel-next",
        prevEl: ".products-carousel-prev",
      },
      breakpoints: {
        0: {
          slidesPerView: 1,
        },
        768: {
          slidesPerView: 3,
        },
        991: {
          slidesPerView: 4,
        },
        1500: {
          slidesPerView: 6,
        },
      }
    });
  }

  var initProductQty = function () {
    $('.product-qty').each(function () {

      var $el_product = $(this);
      console.log("coucou")

      $el_product.find('.quantity-right-plus').click(function (e) {
        e.preventDefault();
        var quantity = parseInt($el_product.find('#quantity').val());
        $el_product.find('#quantity').val(quantity + 1);
      });

      $el_product.find('.quantity-left-minus').click(function (e) {
        e.preventDefault();
        var quantity = parseInt($el_product.find('#quantity').val());
        if (quantity > 0) {
          $el_product.find('#quantity').val(quantity - 1);
        }
      });

    });
  }


  // init jarallax parallax
  var initJarallax = function () {
    jarallax(document.querySelectorAll(".jarallax"));

    jarallax(document.querySelectorAll(".jarallax-keep-img"), {
      keepImg: true,
    });
  }

  // document ready
  $(document).ready(function () {

    initPreloader();
    initSwiper();
    initProductQty();
    initJarallax();
    initChocolat();

  }); // End of a document

})(jQuery);

//// document panier

document.addEventListener('DOMContentLoaded', function () {
  const urlParams = new URLSearchParams(window.location.search);
  const showModal = urlParams.get('showModal');

  if (showModal) {
    const modal = new bootstrap.Modal(document.getElementById('signInModal'));
    modal.show();
  }
});

//   incriment with 1


//********************************************
// Tableau pour stocker les articles du panier
let cartItems = JSON.parse(localStorage.getItem('cart')) || [];

// Fonction pour arrondir correctement les prix à 3 décimales (sans perte de précision)
function formatPrice(value) {
  return parseFloat(value).toFixed(3).replace(',', '.'); // Affichage avec 3 décimales et virgule
}

// Fonction pour ajouter un article au panier
document.querySelectorAll('.add-to-cart').forEach(button => {
  button.addEventListener('click', function () {
    const productElement = this.closest('#col'); // Trouver le produit
    const productId = productElement.dataset.productId;
    const productTitle = productElement.dataset.productTitle;
    const productPrice = productElement.dataset.productPrice;
    const productImageUrl = productElement.dataset.productImageurl;
    console.log(productPrice)
    console.log(productTitle)
    console.log(productImageUrl)


    const productQuantity = parseInt(productElement.querySelector('.quantity').value);


    // Vérifier si le produit existe déjà dans le panier
    const existingProduct = cartItems.find(item => item.id === productId);

    if (existingProduct) {
      // Si le produit est déjà dans le panier, on met à jour la quantité
      existingProduct.quantity += productQuantity;
    } else {
      // Si le produit n'est pas encore dans le panier, on l'ajoute
      cartItems.push({
        id: productId,
        title: productTitle,
        price: productPrice, // Le prix doit être un float
        quantity: productQuantity,
        image: productImageUrl
      });
    }

    // Mettre à jour l'affichage du panier
    updateCartDisplay();

    // Enregistrer les articles dans le localStorage
    localStorage.setItem('cart', JSON.stringify(cartItems));
  });
});

// Fonction pour mettre à jour l'affichage du panier dans l'offcanvas
function updateCartDisplay() {
  const cartItemsList = document.querySelector('.list-group');
  const cartCountBadge = document.querySelector('.badge.bg-primary');
  const CountBadgeAbove = document.querySelector('.cart-badge');

  const cartTotalElement = document.querySelector('.list-group-item strong');

  // Vider la liste actuelle des produits
  cartItemsList.innerHTML = '';

  let totalQuantity = 0;
  let totalPrice = 0;

  cartItems.forEach(item => {
    totalQuantity += item.quantity;
    totalPrice += item.quantity * item.price; // Calculer correctement en utilisant les floats

    // Ajouter chaque produit dans la liste
    const listItem = document.createElement('li');
    listItem.className = 'list-group-item d-flex justify-content-between align-items-center p-3 border-bottom';

// Create the left container (image and details)
    const leftContainer = document.createElement('div');
    leftContainer.className = 'd-flex align-items-center';

// Add the image
    const image = document.createElement('img');
    image.src = item.image;
    image.alt = 'Product Image';
    image.className = 'me-3 rounded-circle shadow-sm';
    image.style.width = '100px';
    image.style.height = '100px';
    image.style.objectFit = 'cover';
    leftContainer.appendChild(image);

    // Add the product details
    const details = document.createElement('div');
    details.innerHTML = `
  <span style="font-size: 12px; font-family: 'Helvetica', sans-serif; color: #333;     letter-spacing: 1px; ">
    ${item.title}
  </span>
  <br>
  <span class="d-block text-muted mt-2" style="font-size: 12px; color: #666;">
    Quantité: ${item.quantity}
  </span>
`;
    leftContainer.appendChild(details);

    // Create the price span
    const price = document.createElement('span');
    price.style.fontWeight = 'bold';
    price.style.fontSize = '16px';
    price.style.color = '#070707'; // Green price color
    price.textContent = `${formatPrice(item.quantity * item.price)} TND`;

// Append leftContainer and price to the list item
    listItem.appendChild(leftContainer);
    listItem.appendChild(price);

// Append the list item to the cart items list
    cartItemsList.appendChild(listItem);
  });

  // Ajouter l'élément du total
  const totalItem = document.createElement('li');
  totalItem.className = 'list-group-item d-flex justify-content-between p-3 border-top';
  totalItem.innerHTML = `
    <span style="font-size: 13px; font-weight: bold; color: #333;">Total (TND)</span>
    <strong style="font-size: 18px; color: #80cc28;">${formatPrice(totalPrice)}</strong>
`;
  cartItemsList.appendChild(totalItem);

// Mettre à jour le badge du panier
  cartCountBadge.textContent = totalQuantity;
  CountBadgeAbove.textContent = totalQuantity;

  cartTotalElement.textContent = `${formatPrice(totalPrice)} TND`;
}

// Récupérer et afficher le panier lors du chargement de la page
document.addEventListener("DOMContentLoaded", function () {
  updateCartDisplay();
});

// Gestion des boutons + et - pour ajuster la quantité
document.querySelectorAll('.quantity-left-minus').forEach(button => {
  button.addEventListener('click', function () {
    const quantityInput = this.closest('.product-qty').querySelector('.quantity');
    let currentValue = parseInt(quantityInput.value, 10);
    if (currentValue > 1) { // S'assurer que la quantité ne descend pas en dessous de 1
      quantityInput.value = currentValue - 1;

      // Mettre à jour le panier localStorage après la modification
      updateCartQuantity(quantityInput, -1);
    }
  });
});

document.querySelectorAll('.quantity-right-plus').forEach(button => {
  button.addEventListener('click', function () {
    const quantityInput = this.closest('.product-qty').querySelector('.quantity');
    let currentValue = parseInt(quantityInput.value, 10);
    quantityInput.value = currentValue; // Incrémenter la quantité de 1

    // Mettre à jour le panier localStorage après la modification
    updateCartQuantity(quantityInput, 1);
  });
});

// Fonction pour mettre à jour la quantité dans le panier et localStorage
function updateCartQuantity(quantityInput, change) {
  const productId = quantityInput.closest('.col').dataset.productId;
  const product = cartItems.find(item => item.id === productId);
  if (product) {
    product.quantity += change;
    // Mettre à jour le localStorage avec les nouvelles données du panier
    localStorage.setItem('cart', JSON.stringify(cartItems));
    updateCartDisplay();
  }
}

// Gestion du bouton de commande (checkout)
document.getElementById('checkout-button').addEventListener('click', function () {
  // Sauvegarder les données dans localStorage
  localStorage.setItem('cart', JSON.stringify(cartItems));

  // Rediriger vers la page de validation
  window.location.href = '/checkout';
});

// Gestion du bouton de vider le panier
document.getElementById('clear-cart').addEventListener('click', function () {
  // Réinitialiser le panier local (tableau cartItems)
  cartItems = [];

  // Vider le panier dans le localStorage
  localStorage.removeItem('cart');

  // Mettre à jour l'affichage du panier (en affichant un panier vide)
  updateCartDisplay();
});


document.getElementById('checkout-button').addEventListener('click', function () {
  // Sauvegarder les données dans localStorage
  localStorage.setItem('cart', JSON.stringify(cartItems));

  // Rediriger vers la page de validation
  window.location.href = '/checkout';
});


document.getElementById('clear-cart').addEventListener('click', function () {
  // Réinitialiser le panier local (tableau cartItems)
  cartItems = [];

  // Vider le panier dans le localStorage
  localStorage.removeItem('cart');

  // Mettre à jour l'affichage du panier (en affichant un panier vide)
  updateCartDisplay();
});



  document.addEventListener("DOMContentLoaded", function () {
  document.querySelectorAll(".dropdown-menu li, .dropdown-menu span").forEach(item => {
    item.addEventListener("click", function () {
      let paramName = this.tagName === "LI" ? "gamme" : "subcategorie";
      console.log("coucou")
      let paramValue = this.textContent.trim();
      window.location.href = `/products?${paramName}=${encodeURIComponent(paramValue)}`;
    });
  });
});



