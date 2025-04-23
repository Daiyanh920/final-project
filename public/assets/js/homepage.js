//console.log('calling homepage')
 
function addIncome() 
{
    const description = $('#income-description').val();
    const amount = $('#income-amount').val();
  
    $.ajax({
      url: '/?url=transactions',
      method: 'POST',
      contentType: 'application/json',
      data: JSON.stringify({
        description: description,
        amount: amount,
        type: 'income'
      }),
      success: function (response) {
        alert(response.message || 'Income added');
        loadTransactions();
      },
      error: function () {
        alert('Error adding income.');
      }
    });
}

function addExpense() 
{
    const description = $('#expense-description').val();
    const amount = $('#expense-amount').val();
    const category = $('#expense-category').val();
  
    $.ajax({
      url: '/?url=transactions',
      method: 'POST',
      contentType: 'application/json',
      data: JSON.stringify({
        description: description,
        amount: amount,
        category: category,
        type: 'expense'
      }),
      success: function (response) {
        alert(response.message || 'Expense added');
        loadTransactions();
      },
      error: function () {
        alert('Error adding expense.');
      }
    });
}
  
function loadTransactions() 
{
    $.ajax({
      url: '?url=transactions',
      method: 'GET',
      success: function (data) {
        console.log("Loaded transaction:", data)
        let totalIncome = 0;
        let totalExpenses = 0;
  
        $('#transaction-history').empty();
  
        data.forEach(function (tx) {
          $('#transaction-history').append(`
            <tr>
              <td>${tx.description}</td>
              <td>${tx.category || '-'}</td>
              <td>${tx.amount}</td>
              <td>${tx.type}</td>
              <td><button onclick="deleteTransaction(${tx.id})">Delete</button></td>
            </tr>
          `);
  
          if (tx.type === 'income') {
            totalIncome += parseFloat(tx.amount);
          } else {
            totalExpenses += parseFloat(tx.amount);
          }
        });
  
        $('#total-income').text(totalIncome.toFixed(2));
        $('#total-expenses').text(totalExpenses.toFixed(2));
        $('#balance').text((totalIncome - totalExpenses).toFixed(2));
      },
      error: function () {
        alert('Error loading transactions.');
      }
    });
}

  $(document).ready(function () {
    loadTransactions();
  });

function deleteTransaction(id)
{
    if (!confirm('Are you sure you want to delete this transaction?')) return; //Ask the user are you sure you want to delete 
  
    $.ajax({
      url: '/?url=transactions',
      method: 'DELETE',
      success: function (response) {
        alert(response.message || 'Transaction deleted');
        loadTransactions(); // reload the table
      },
      error: function () {
        alert('Failed to delete transaction.');
      }
    });
}
  
function searchStock() 
{
  const symbol = $('#stock-symbol').val().toUpperCase();

  if (!symbol) {
    $('#stock-result').html('Please enter a stock symbol.');
    return;
  }

  $.ajax({
    url: '/?url=stock-price&symbol=' + symbol,
    method: 'GET',
    success: function (data) {
      $('#stock-result').html(`<strong>${symbol}</strong> current price: $${data.c}`);
    },
    error: function () {
      $('#stock-result').html('Failed to load stock data.');
    }
  });
}

function logout() 
{
  $.ajax({
    url: '/?url=logout',
    method: 'POST',
    success: function (res) {
      alert(res.message || 'Logged out');
      window.location.href = '/?url=login-page';
    },
    error: function () {
      alert('Failed to logout');
    }
  });
}