function searchStock() 
{
    const symbol = $('#stock-symbol').val().toUpperCase();
  
    if (!symbol) {
      $('#stock-result').html('Please enter a stock symbol.');
      return;
    }
  
    $.ajax({
      url: `/stock-price?symbol=${symbol}`,
      method: 'GET',
      success: function (data) {
        const parsed = typeof data === 'string' ? JSON.parse(data) : data;
        console.log("Parsed stock data:", parsed);

        $('#stock-result').html(`<strong>${symbol}</strong> current price: $${parsed.c}`);
        $('#buy-section').show();
        $('#buy-section').data('price', parsed.c);       
    },
    error: function () {
      $('#stock-result').html('Failed to load stock data.');
    }
  });
}

function buyStock() {
  const symbol = $('#stock-symbol').val().toUpperCase();
  const quantity = parseInt($('#stock-quantity').val());
  const price = $('#buy-section').data('price');

  console.log("Sending buy data:", {
   symbol: symbol,
    quantity: quantity,
    price: price
  });


  if (!symbol || !quantity || quantity <= 0) {
    $('#buy-message').text('Enter a valid quantity.');
    return;
  }

  $.ajax({
    url: '/buy-stock',
    method: 'POST',
    contentType: 'application/json',
    data: JSON.stringify({
      symbol: symbol,
      quantity: quantity,
      price: price
    }),
    success: function (res) {
      $('#buy-message').text(res.message || 'Stock purchased!');
    },
    error: function () {
      $('#buy-message').text('Failed to complete purchase.');
    }
  });
}

function loadPortfolio() 
{
  $.ajax({
    url: '/portfolio',
    method: 'GET',
    success: function (data) {
      $('#portfolio-table').empty();
      data.forEach(row => {
        const symbol = row.symbol;
        const avgPrice = parseFloat(row.avg_price);
        const quantity = parseInt(row.total_quantity);
        const totalInvested = parseFloat(row.total_invested);

        // Fetch current price from Finnhub
        $.ajax({
          url: `/stock-price?symbol=${symbol}`,
          method: 'GET',
          success: function (liveData) 
          {
            const currentPrice = parseFloat(liveData.c);
            const currentValue = currentPrice * quantity;
            const gainLoss = currentValue - totalInvested;
            const gainLossPercent = ((gainLoss / totalInvested) * 100).toFixed(2);

            $('#portfolio-table').append(`
              <tr>
                <td>${symbol}</td>
                <td>${quantity}</td>
                <td>$${totalInvested.toFixed(2)}</td>
                <td>$${currentPrice.toFixed(2)}</td>
                <td>${gainLoss >= 0 ? '💚' : '❤️'} $${gainLoss.toFixed(2)} (${gainLossPercent}%)</td>
              </tr>
            `);
          },
          error: function () {
            $('#portfolio-table').append(`
              <tr>
                <td>${symbol}</td>
                <td>${quantity}</td>
                <td>$${totalInvested.toFixed(2)}</td>
                <td colspan="2">❌ Live price unavailable</td>
              </tr>
            `);
          }
        });
      });
    },

    error: function () {
      $('#portfolio-table').html('<tr><td colspan="3">Failed to load portfolio</td></tr>');
    }

  });
}

$(document).ready(function () {
  loadPortfolio();
});

function sellStock() {
  const symbol = $('#stock-symbol').val().toUpperCase();
  const quantity = parseInt($('#sell-quantity').val());

  if (!symbol || !quantity || quantity <= 0) {
    $('#sell-message').text('Enter a valid quantity.');
    return;
  }

  $.ajax({
    url: '/sell-stock',
    method: 'POST',
    contentType: 'application/json',
    data: JSON.stringify({ symbol, quantity }),
    success: function (res) {
      $('#sell-message').text(res.message || 'Sold!');
      loadPortfolio(); // refresh table
    },
    error: function () {
      $('#sell-message').text('Failed to complete sale.');
    }
  });
}