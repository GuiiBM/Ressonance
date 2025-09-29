<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Ressonance</title>
    <link rel="stylesheet" href="../../../public/assets/css/styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="login-container">
        <div class="login-box">
            <div class="logo">RESSONANCE</div>
            <h2>Entre na sua conta</h2>
            
            <a href="<?= 'https://accounts.google.com/oauth/authorize?' . http_build_query([
                'client_id' => 'COLE_SEU_CLIENT_ID_AQUI',
                'redirect_uri' => 'http://localhost/Ressonance/app/config/auth.php',
                'scope' => 'openid profile email',
                'response_type' => 'code'
            ]) ?>" class="google-login-btn">
                <i class="fab fa-google"></i>
                Entrar com Google
            </a>
            
            <div class="admin-login">
                <form method="POST" action="admin_login.php">
                    <input type="email" name="email" placeholder="Email do Admin" required>
                    <input type="password" name="password" placeholder="Senha" required>
                    <button type="submit">Login Admin</button>
                </form>
            </div>
            
            <a href="../../../index.php" class="back-link">← Voltar ao início</a>
        </div>
    </div>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Montserrat', sans-serif;
            background: linear-gradient(135deg, #121212 0%, #1d1d1d 100%);
            min-height: 100vh;
        }
        
        .login-container {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 1rem;
        }
        
        .login-box {
            background: rgba(29, 29, 29, 0.95);
            backdrop-filter: blur(10px);
            padding: 2rem;
            border-radius: 16px;
            text-align: center;
            width: 100%;
            max-width: 400px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.3);
            border: 1px solid rgba(255,255,255,0.1);
        }
        
        .login-box .logo {
            font-size: 2.5rem;
            font-weight: 700;
            color: #1db954;
            margin-bottom: 0.5rem;
            text-shadow: 0 2px 4px rgba(29, 185, 84, 0.3);
        }
        
        .login-box h2 {
            margin-bottom: 2rem;
            color: #ffffff;
            font-weight: 400;
            font-size: 1.2rem;
        }
        
        .google-login-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.75rem;
            background: linear-gradient(135deg, #1db954 0%, #1ed760 100%);
            color: #000000;
            text-decoration: none;
            padding: 1rem 2rem;
            border-radius: 50px;
            font-weight: 600;
            transition: all 0.3s ease;
            margin-bottom: 2rem;
            width: 100%;
            box-shadow: 0 4px 15px rgba(29, 185, 84, 0.3);
        }
        
        .google-login-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(29, 185, 84, 0.4);
        }
        
        .admin-login {
            border-top: 1px solid rgba(255,255,255,0.1);
            padding-top: 2rem;
            margin-top: 2rem;
        }
        
        .admin-login input {
            width: 100%;
            padding: 1rem;
            margin-bottom: 1rem;
            background: rgba(40, 40, 40, 0.8);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 8px;
            color: #ffffff;
            font-size: 1rem;
            transition: border-color 0.3s ease;
        }
        
        .admin-login input:focus {
            outline: none;
            border-color: #1db954;
            box-shadow: 0 0 0 2px rgba(29, 185, 84, 0.2);
        }
        
        .admin-login input::placeholder {
            color: #b3b3b3;
        }
        
        .admin-login button {
            background: linear-gradient(135deg, #ff4b5a 0%, #ff6b7a 100%);
            color: #ffffff;
            border: none;
            padding: 1rem 2rem;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            width: 100%;
            font-size: 1rem;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(255, 75, 90, 0.3);
        }
        
        .admin-login button:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(255, 75, 90, 0.4);
        }
        
        .back-link {
            color: #b3b3b3;
            text-decoration: none;
            margin-top: 2rem;
            display: inline-block;
            transition: color 0.3s ease;
        }
        
        .back-link:hover {
            color: #1db954;
        }
        
        @media (max-width: 480px) {
            .login-box {
                padding: 1.5rem;
                margin: 1rem;
            }
            
            .login-box .logo {
                font-size: 2rem;
            }
            
            .login-box h2 {
                font-size: 1.1rem;
            }
            
            .google-login-btn {
                padding: 0.875rem 1.5rem;
                font-size: 0.9rem;
            }
            
            .admin-login input,
            .admin-login button {
                padding: 0.875rem;
                font-size: 0.9rem;
            }
        }
        
        @media (max-width: 320px) {
            .login-container {
                padding: 0.5rem;
            }
            
            .login-box {
                padding: 1rem;
            }
            
            .login-box .logo {
                font-size: 1.8rem;
            }
        }
    </style>
</body>
</html>