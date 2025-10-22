<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

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

$message = "";
$success = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION['user_id'];
    $name = trim($_POST['name']);
    $phone = trim($_POST['phone']);
    $address = trim($_POST['address']);
    $postal_code = trim($_POST['postal-code']);
    $city = trim($_POST['city']);
    $email = trim($_POST['email']);
    $cell_brand = trim($_POST['cell-brand']);
    $cell_model = trim($_POST['cell-model']);
    $collection_service = $_POST['collection-service'];
    $description = trim($_POST['description']);

    // Handle file uploads
    $upload_dir = 'uploads/';
    if (!is_dir($upload_dir)) {
        if (!mkdir($upload_dir, 0777, true)) {
            $message = "Failed to create upload directory.";
        }
    }

    $picture1 = '';
    $picture2 = '';
    $picture3 = '';
    $upload_errors = [];

    // Upload Picture 1 (required)
    if (isset($_FILES['picture1'])) {
        if ($_FILES['picture1']['error'] == 0) {
            $picture1 = $upload_dir . basename($_FILES['picture1']['name']);
            if (!move_uploaded_file($_FILES['picture1']['tmp_name'], $picture1)) {
                $upload_errors[] = "Failed to upload Picture 1.";
            }
        } else {
            $upload_errors[] = "Error uploading Picture 1: " . $_FILES['picture1']['error'];
        }
    }

    // Upload Picture 2 (optional)
    if (isset($_FILES['picture2']) && $_FILES['picture2']['error'] == 0) {
        $picture2 = $upload_dir . basename($_FILES['picture2']['name']);
        if (!move_uploaded_file($_FILES['picture2']['tmp_name'], $picture2)) {
            $upload_errors[] = "Failed to upload Picture 2.";
        }
    }

    // Upload Picture 3 (optional)
    if (isset($_FILES['picture3']) && $_FILES['picture3']['error'] == 0) {
        $picture3 = $upload_dir . basename($_FILES['picture3']['name']);
        if (!move_uploaded_file($_FILES['picture3']['tmp_name'], $picture3)) {
            $upload_errors[] = "Failed to upload Picture 3.";
        }
    }

    if (!empty($upload_errors)) {
        $message = implode(' ', $upload_errors);
    } else {
        // Proceed with database insert only if no upload errors
        // Insert into database
        $stmt = $conn->prepare("INSERT INTO repair_requests (user_id, name, phone, address, postal_code, city, email, cell_brand, cell_model, collection_service, picture1, picture2, picture3, description) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("isssssssssssss", $user_id, $name, $phone, $address, $postal_code, $city, $email, $cell_brand, $cell_model, $collection_service, $picture1, $picture2, $picture3, $description);

        if ($stmt->execute()) {
            $success = true;
            $message = "Your budget request has been submitted successfully. We will contact you shortly.";
        } else {
            $message = "Error: " . $stmt->error;
        }

        $stmt->close();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Budget Request - iMobile</title>
    
    <style>
        /* Import a clean font similar to the image */
        @import url('https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap');

        /* --- Global Styles --- */
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Roboto', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #333;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
            min-height: 100vh;
        }

        /* --- Form Container --- */
        .form-container {
            width: 100%;
            max-width: 700px;
            background-color: #ffffff;
            border-radius: 12px;
            padding: 40px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
            position: relative;
            overflow: hidden;
        }

        .form-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #00a651, #00843d);
        }

        h1 {
            font-size: 28px;
            font-weight: 700;
            color: #1a1a1a;
            margin-bottom: 30px;
            text-align: center;
            position: relative;
        }

        h1::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            width: 60px;
            height: 3px;
            background: #00a651;
            border-radius: 2px;
        }

        /* --- Form Elements --- */
        form {
            display: flex;
            flex-direction: column;
            gap: 20px; /* Space between form groups */
        }

        .form-group {
            display: flex;
            flex-direction: column;
        }

        /* Labels and Required Asterisk */
        label,
        legend {
            font-size: 15px;
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
        }

        .required {
            color: #e74c3c;
            margin-left: 4px;
            font-size: 16px;
        }

        /* Text Inputs, Tel, Email, and Textarea */
        input[type="text"],
        input[type="tel"],
        input[type="email"],
        textarea {
            width: 100%;
            padding: 14px 16px;
            font-size: 16px;
            border: 2px solid #e1e8ed;
            border-radius: 8px;
            background-color: #fafbfc;
            transition: all 0.3s ease;
            font-family: inherit;
        }

        input[type="text"]::placeholder,
        input[type="tel"]::placeholder,
        input[type="email"]::placeholder,
        textarea::placeholder {
            color: #95a5a6;
            font-size: 15px;
        }

        input[type="text"]:focus,
        input[type="tel"]:focus,
        input[type="email"]:focus,
        textarea:focus {
            outline: none;
            border-color: #00a651;
            background-color: #ffffff;
            box-shadow: 0 0 0 3px rgba(0, 166, 81, 0.1);
            transform: translateY(-1px);
        }

        textarea {
            resize: vertical;
            min-height: 120px;
            line-height: 1.5;
        }

        /* --- Special Layouts --- */

        /* Row for Postal Code and City */
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .form-row .form-group {
            flex: 1;
        }

        /* Radio Buttons (Collection Service) */
        fieldset {
            border: 2px solid #e1e8ed;
            border-radius: 8px;
            padding: 20px;
            background-color: #fafbfc;
            margin-top: 8px;
        }

        legend {
            padding: 0 10px;
            font-size: 16px;
            font-weight: 600;
            color: #2c3e50;
            background-color: #ffffff;
            border-radius: 4px;
        }

        .radio-options {
            display: flex;
            gap: 30px;
            margin-top: 10px;
        }

        .radio-group {
            display: flex;
            align-items: center;
            gap: 10px;
            cursor: pointer;
            padding: 8px 12px;
            border-radius: 6px;
            transition: background-color 0.2s;
        }

        .radio-group:hover {
            background-color: rgba(0, 166, 81, 0.05);
        }

        .radio-group label {
            margin-bottom: 0;
            font-weight: 500;
            color: #34495e;
            cursor: pointer;
        }

        input[type="radio"] {
            width: 18px;
            height: 18px;
            accent-color: #00a651;
            cursor: pointer;
        }

        /* --- Custom File Upload Buttons --- */

        /* Hide the default file input */
        .file-input {
            width: 0.1px;
            height: 0.1px;
            opacity: 0;
            overflow: hidden;
            position: absolute;
            z-index: -1;
        }

        /* Style the label as a button */
        .file-label {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 12px 20px;
            font-size: 15px;
            font-weight: 600;
            color: #fff;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
            width: fit-content;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            position: relative;
            overflow: hidden;
        }

        .file-label::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s;
        }

        .file-label:hover::before {
            left: 100%;
        }

        .file-label-text {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 200px;
            position: relative;
            z-index: 1;
        }

        /* SVG Icon for upload */
        .file-label svg {
            width: 18px;
            height: 18px;
            fill: currentColor;
            margin-right: 10px;
            position: relative;
            z-index: 1;
        }

        /* "Picture 1" button (orange gradient) */
        .file-label.required-file {
            background: linear-gradient(135deg, #f39c12, #e67e22);
        }
        .file-label.required-file:hover {
            background: linear-gradient(135deg, #e67e22, #d35400);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(243, 156, 18, 0.3);
        }

        /* "Picture 2" & "Picture 3" buttons (blue gradient) */
        .file-label.optional-file {
            background: linear-gradient(135deg, #3498db, #2980b9);
        }
        .file-label.optional-file:hover {
            background: linear-gradient(135deg, #2980b9, #21618c);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(52, 152, 219, 0.3);
        }

        /* --- Submit Button --- */
        .submit-btn {
            width: 100%;
            padding: 16px;
            font-size: 18px;
            font-weight: 700;
            color: #fff;
            background: linear-gradient(135deg, #00a651, #00843d);
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 20px;
            box-shadow: 0 4px 15px rgba(0, 166, 81, 0.2);
            position: relative;
            overflow: hidden;
        }

        .submit-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s;
        }

        .submit-btn:hover::before {
            left: 100%;
        }

        .submit-btn:hover {
            background: linear-gradient(135deg, #00843d, #006b32);
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 166, 81, 0.3);
        }

        /* --- Success Message --- */
        .success-message {
            text-align: center;
            padding: 40px 20px;
            background: linear-gradient(135deg, #d4edda, #c3e6cb);
            border-radius: 12px;
            border: 2px solid #c3e6cb;
        }
        .success-message h2 {
            color: #155724;
            margin-bottom: 15px;
            font-size: 24px;
        }
        .success-message p {
            color: #155724;
            font-size: 16px;
            margin-bottom: 10px;
        }

        /* --- Message Styles --- */
        .message {
            padding: 18px 20px;
            border-radius: 8px;
            margin-bottom: 25px;
            text-align: center;
            font-weight: 500;
            border-left: 4px solid;
        }

        .success {
            background: linear-gradient(135deg, #d4edda, #c3e6cb);
            color: #155724;
            border-left-color: #28a745;
        }

        .error {
            background: linear-gradient(135deg, #f8d7da, #f5c6cb);
            color: #721c24;
            border-left-color: #dc3545;
        }

        /* Navigation Link */
        .nav-link {
            display: inline-block;
            margin-top: 25px;
            padding: 12px 24px;
            background: linear-gradient(135deg, #6c757d, #495057);
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .nav-link:hover {
            background: linear-gradient(135deg, #495057, #343a40);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.2);
        }

        /* --- Responsive Design --- */
        @media (max-width: 768px) {
            body {
                padding: 10px;
            }

            .form-container {
                padding: 25px;
                margin: 10px;
            }

            h1 {
                font-size: 24px;
            }

            .form-row {
                grid-template-columns: 1fr;
                gap: 20px;
            }

            .radio-options {
                flex-direction: column;
                gap: 15px;
            }

            .file-label {
                width: 100%;
                justify-content: center;
            }

            .file-label-text {
                max-width: none;
            }
        }

        @media (max-width: 480px) {
            .form-container {
                padding: 20px;
            }

            h1 {
                font-size: 22px;
            }

            input[type="text"],
            input[type="tel"],
            input[type="email"],
            textarea {
                padding: 12px;
                font-size: 16px; /* Prevent zoom on iOS */
            }
        }
    </style>
</head>
<body>

    <div class="form-container" id="formContainer">
        <h1>BUDGET REQUEST</h1>

        <?php if (!empty($message)): ?>
            <div class="message <?php echo $success ? 'success' : 'error'; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <?php if (!$success): ?>
        <form id="budgetForm" action="" method="POST" enctype="multipart/form-data" novalidate>
            
            <div class="form-group">
                <label for="name">Name <span class="required">*</span></label>
                <input type="text" id="name" name="name" placeholder="Ex.: John" required>
            </div>

            <div class="form-group">
                <label for="phone">Phone Number <span class="required">*</span></label>
                <input type="tel" id="phone" name="phone" placeholder="Ex.: +351 987 654 321" required>
            </div>

            <div class="form-group">
                <label for="address">Address <span class="required">*</span></label>
                <input type="text" id="address" name="address" placeholder="Ex.: Almirante Reis Avenue" required>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="postal-code">Postal Code <span class="required">*</span></label>
                    <input type="text" id="postal-code" name="postal-code" placeholder="Ex.: 1150-008" required>
                </div>
                <div class="form-group">
                    <label for="city">City <span class="required">*</span></label>
                    <input type="text" id="city" name="city" placeholder="Ex.: Lisbon" required>
                </div>
            </div>

            <div class="form-group">
                <label for="email">Email <span class="required">*</span></label>
                <input type="email" id="email" name="email" placeholder="Ex.: john@example.com" required>
            </div>

            <div class="form-group">
                <label for="cell-brand">Cellphone Brand <span class="required">*</span></label>
                <input type="text" id="cell-brand" name="cell-brand" placeholder="Ex.: Apple" required>
            </div>

            <div class="form-group">
                <label for="cell-model">Cellphone Model <span class="required">*</span></label>
                <input type="text" id="cell-model" name="cell-model" placeholder="Ex.: iPhone 12 Pro Max" required>
            </div>

            <div class="form-group">
                <fieldset>
                    <legend>Collection Service <span class="required">*</span></legend>
                    <div class="radio-options">
                        <div class="radio-group">
                            <input type="radio" id="collection-yes" name="collection-service" value="yes" required>
                            <label for="collection-yes">Yes</label>
                        </div>
                        <div class="radio-group">
                            <input type="radio" id="collection-no" name="collection-service" value="no" required>
                            <label for="collection-no">No</label>
                        </div>
                    </div>
                </fieldset>
            </div>

            <div class="form-group">
                <label for="picture1">Picture 1 <span class="required">*</span></label>
                <input type="file" id="picture1" name="picture1" class="file-input" required>
                <label for="picture1" class="file-label required-file">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M9 16h6v-6h4l-7-7-7 7h4v6zm-4 4h14v-2H5v2z"/></svg>
                    <span class="file-label-text">UPLOAD FILE</span>
                </label>
            </div>

            <div class="form-group">
                <label for="picture2">Picture 2</label>
                <input type="file" id="picture2" name="picture2" class="file-input">
                <label for="picture2" class="file-label optional-file">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M9 16h6v-6h4l-7-7-7 7h4v6zm-4 4h14v-2H5v2z"/></svg>
                    <span class="file-label-text">UPLOAD FILE</span>
                </label>
            </div>

            <div class="form-group">
                <label for="picture3">Picture 3</label>
                <input type="file" id="picture3" name="picture3" class="file-input">
                <label for="picture3" class="file-label optional-file">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M9 16h6v-6h4l-7-7-7 7h4v6zm-4 4h14v-2H5v2z"/></svg>
                    <span class="file-label-text">UPLOAD FILE</span>
                </label>
            </div>

            <div class="form-group">
                <label for="description">Problem Description <span class="required">*</span></label>
                <textarea id="description" name="description" placeholder="Problem Description" rows="5" required></textarea>
            </div>

            <button type="submit" class="submit-btn">Get Budget</button>

        </form>
        <?php endif; ?>

        <div style="text-align: center;">
            <a href="dashboard.php" class="nav-link">← Back to Dashboard</a>
        </div>
    </div>

    <script>
        // Wait for the DOM to be fully loaded before running the script
        document.addEventListener('DOMContentLoaded', () => {

            // --- File Input Label Changer ---
            // This script updates the "UPLOAD FILE" button text to show the selected file name.

            const fileInputs = document.querySelectorAll('.file-input');

            fileInputs.forEach(input => {
                input.addEventListener('change', (e) => {
                    // Find the label associated with this input
                    const label = document.querySelector(`label[for="${e.target.id}"]`);
                    // Find the span inside the label that holds the text
                    const labelText = label.querySelector('.file-label-text');

                    // Store the original text (e.g., "UPLOAD FILE") if it's not already stored
                    if (!labelText.dataset.originalText) {
                        labelText.dataset.originalText = labelText.textContent;
                    }

                    if (e.target.files.length > 0) {
                        // A file was selected!
                        let fileName = e.target.files[0].name;

                        // Truncate long file names to prevent layout breaking
                        if (fileName.length > 25) {
                            fileName = fileName.substring(0, 22) + '...';
                        }
                        
                        labelText.textContent = fileName;
                    } else {
                        // No file was selected (or the user cancelled)
                        // Restore the original text
                        labelText.textContent = labelText.dataset.originalText;
                    }
                });
            });

            // --- Form Submission Handler ---
            // This script intercepts the form submission to show a success message
            // instead of reloading the page.

            const form = document.getElementById('budgetForm');
            const formContainer = document.getElementById('formContainer');

            form.addEventListener('submit', (e) => {
                // Let the form submit normally to PHP for processing
                // The PHP code will handle the database insertion and show success/error messages
            });

        });
    </script>

</body>
</html>