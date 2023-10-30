// pricing/static/pricing/checkout.js

$(document).ready(function () {
  let cbInstance = Chargebee.init({
    site: 'honeycomics-test', // replace with your site name
  })
  let cbInstance = Chargebee.getInstance()
  $('.checkout-button').click(function () {
    let selectedPriceId = $(this).data('price')
    console.log(selectedPriceId)
    const csrftoken = getCookie('csrftoken')
    console.log(csrftoken)

    openChargebeeCheckout(selectedPriceId, csrftoken)
  })
  function openChargebeeCheckout(priceId, csrftoken) {
    cbInstance.openCheckout({
      hostedPage: function () {
        return $.ajax({
          headers: { 'X-CSRFToken': csrftoken },
          method: 'post',
          url: 'http://localhost:8000/api/generate_checkout_new_url', // url point to "CreateCBSubscriptionView"
          data: { price_id: priceId },
        })
      },
      loaded: function () {
        console.log('checkout opened')
      },
      error: function () {
        $('#loader').hide()
        $('#errorContainer').show()
      },
      close: function () {
        $('#loader').hide()
        $('#errorContainer').hide()
        console.log('checkout closed')
      },
      success: function (hostedPageId) {
           window.location.replace("http://localhost:8000/subscription");
      },
      step: function (value) {
        console.log(value)
      },
    })
  }

  function getCookie(name) {
    let cookieValue = null
    if (document.cookie && document.cookie !== '') {
      const cookies = document.cookie.split(';')
      for (let i = 0; i < cookies.length; i++) {
        const cookie = cookies[i].trim()
        if (cookie.substring(0, name.length + 1) === name + '=') {
          cookieValue = decodeURIComponent(cookie.substring(name.length + 1))
          break
        }
      }
    }
    return cookieValue
  }
})
