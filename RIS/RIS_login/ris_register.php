<!DOCTYPE html>
<html>
<head>
    <title>User Registration</title>
    <style>
        body {
            font-family: "Segoe UI", Tahoma, sans-serif;
            background: #f4f9f4; /* very light greenish background */
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .container {
            background: #ffffff;
            padding: 40px 35px;
            border-radius: 12px;
            box-shadow: 0px 6px 20px rgba(0, 0, 0, 0.08);
            width: 380px;
        }

        h2 {
            text-align: center;
            color: #2e7d32; /* professional green */
            margin-bottom: 25px;
            font-size: 24px;
            font-weight: 600;
        }

        label {
            font-weight: 500;
            color: #444;
            display: block;
            margin-bottom: 6px;
        }

        input, select {
            width: 100%;
            padding: 12px;
            margin-bottom: 18px;
            border: 1px solid #d0e6d0;
            border-radius: 8px;
            background-color: #fcfcf4; /* light yellowish background */
            font-size: 14px;
            transition: all 0.25s ease;
        }

        input:focus, select:focus {
            border-color: #4caf50;
            box-shadow: 0 0 6px rgba(76, 175, 80, 0.4);
            background-color: #ffffff;
            outline: none;
        }

        button {
            width: 100%;
            padding: 14px;
            background: #388e3c;
            color: white;
            font-size: 15px;
            font-weight: 600;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: 0.3s ease;
        }

        button:hover {
            background: #2e7d32;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(46, 125, 50, 0.25);
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Register</h2>
        <form action="ris_register_process.php" method="POST">
            <label>Full Name</label>
            <input type="text" name="full_name" required>

            <label>Email</label>
            <input type="email" name="email" required>

            <label>Password</label>
            <input type="password" name="password" required>

            <label>Role</label>
            <select name="role" required>
                <option value="admin">Admin</option>
                <option value="staff">Staff</option>
                <option value="user">User</option>
            </select>

            <button type="submit">Register</button>
        </form>
    </div>
</body>
</html>
