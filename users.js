// Define the endpoint
const endpoint = 'https://auth-web-api.onrender.com/api/users';

// Function to fetch user data and display it in the table
async function fetchAndDisplayUsers() {
    try {
        // Fetch data from the API
        const response = await fetch(endpoint);
        
        // Check if the response is ok (status in the range 200-299)
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }

        // Parse the JSON data
        const users = await response.json();
        
        // Get the table body element
        const tableBody = document.getElementById('userTableBody');
        
        // Clear the table body
        tableBody.innerHTML = '';

        // Loop through the users and create a table row for each
        users.forEach(user => {
            // Create a new row
            const row = document.createElement('tr');

            console.log(user)

            // Create cells and fill them with data
            row.innerHTML = `
                <td><a href="apps-ecommerce-order-details.html" class="fw-medium link-primary">#VZ${Math.floor(Math.random() * 10000)}</a></td>
                <td>
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 me-2">
                            <img src="assets/images/users/avatar-1.jpg" alt="" class="avatar-xs rounded-circle" />
                        </div>
                        <div class="flex-grow-1">${user.name}</div>
                    </div>
                </td>
                <td>Clothes</td>
                <td><span class="text-success">$109.00</span></td>
                <td>Zoetic Fashion</td>
                <td><span class="badge badge-soft-success">Paid</span></td>
                <td>
                    <h5 class="fs-14 fw-medium mb-0">5.0<span class="text-muted fs-11 ms-1">(61 votes)</span></h5>
                </td>
            `;

            // Append the new row to the table body
            tableBody.appendChild(row);
        });
    } catch (error) {
        console.error('Error fetching user data:', error);
    }
}

// Call the function to fetch and display users when the document is loaded
document.addEventListener('DOMContentLoaded', fetchAndDisplayUsers);
