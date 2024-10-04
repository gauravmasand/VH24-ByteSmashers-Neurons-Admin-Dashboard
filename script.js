function generateResponse() {
    var text = document.getElementById("text").value;
    var responseElement = document.getElementById("response");

    fetch("response.php", {
        method: "POST",
        headers: {
            'Content-Type': 'application/json', // Set content type to JSON
        },
        body: JSON.stringify({ text: text }) // Send the text as JSON
    })
    .then(res => res.json()) // Convert the response to JSON
    .then(res => {
        if (res.error) {
            responseElement.innerHTML = "Error: " + res.error;
        } else {
            responseElement.innerHTML = "Response: " + res.response;
        }
    })
    .catch(err => {
        console.error("Error:", err);
    });
}
