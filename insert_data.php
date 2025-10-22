<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "imobile";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Insert hero data
$conn->query("INSERT INTO hero (title, description, cta_text) VALUES (
    'Trust our repair and maintenance services for your devices',
    'iMobile is committed to always providing customers with the best solution for repairing or maintaining their electronic devices.',
    'Get a Quote'
)");

// Insert services data
$conn->query("INSERT INTO services (title, description) VALUES (
    'We have the perfect solution for you',
    'iMobile specializes in the maintenance and repair of electronic devices, from the simplest and most common problems to the most complex ones, through a team of trusted professionals.'
)");

// Insert features data
$features = [
    ["fas fa-users", "Professionals", "Our team consists of qualified professionals in the maintenance and repair of various electronic devices."],
    ["fas fa-certificate", "Quality", "Our services are highly qualified and we always use the best components with maximum quality."],
    ["fas fa-shield-alt", "Warranty", "All our services come with a warranty in accordance with current legislation."],
    ["fas fa-smile", "Satisfaction", "Providing the best solution and ensuring customer satisfaction is our greatest goal."]
];

foreach ($features as $feature) {
    $conn->query("INSERT INTO features (icon, title, description) VALUES ('$feature[0]', '$feature[1]', '$feature[2]')");
}

// Insert quote data
$conn->query("INSERT INTO quote (text, author, position) VALUES (
    '\"We\'re here to put a dent in the universe. Otherwise, why even be here?\"',
    'Steve Jobs',
    'Founder of Apple Inc.'
)");

// Insert newsletter data
$conn->query("INSERT INTO newsletter (title, description) VALUES (
    'Subscribe to our Newsletter',
    'Want to know about iMobile\'s news, promotions, and offers?'
)");

// Insert contact data
$contacts = [
    ["phone", "Phone", "+351 91 419 11 22"],
    ["email", "Email", "info@imobile.pt"],
    ["address", "Address", "Physical presence at FT Telecommunications store<br>Alameda D. Domingos de Pinho Brandão, 16<br>4540-101 Arouca - Portugal"]
];

foreach ($contacts as $contact) {
    $conn->query("INSERT INTO contact (type, title, value) VALUES ('$contact[0]', '$contact[1]', '$contact[2]')");
}

// Insert footer data
$conn->query("INSERT INTO footer (text) VALUES ('&copy; 2025 iMobile. All rights reserved.')");

echo "Data inserted successfully!";
$conn->close();
?>