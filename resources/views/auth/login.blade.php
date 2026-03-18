@extends('layouts.guest')

@section('content')
<div class="login-container">
    @if(Session::has('success'))
    <div class="alert alert-success">
        {{ Session::get('success') }}
    </div>
    @endif
    @if(Session::has('error'))
    <div class="alert alert-error">
        {{ Session::get('error') }}
    </div>
    @endif
    <div class="col-lg-6 logo-section">
        <div class="auth-cover-wrapper" style="background: url('{{ asset('images/logo01.jpg') }}') no-repeat center center; background-size: contain; min-height: 100vh;">
        </div>
    </div>
    <div class="col-lg-6 form-section">
        <div class="form-container">
            <form action="{{ route('login') }}" method="POST">
                @csrf
                <div class="input-group">
                    <input @error('email') class="is-invalid" @enderror 
                        type="email" 
                        name="email" 
                        id="email" 
                        placeholder="Correo Electrónico" 
                        required 
                        autocomplete="email" 
                        autofocus>
                                @error('email')
                        <span class="error-message">{{ $message }}</span>
                                @enderror
                            </div>

                <div class="input-group">
                    <input type="password" 
                        @error('password') class="is-invalid" @enderror 
                        name="password" 
                        id="password" 
                        placeholder="Contraseña" 
                        required 
                        autocomplete="current-password">
                                @error('password')
                        <span class="error-message">{{ $message }}</span>
                                @enderror
                            </div>

                <div class="options-group">
                    <div class="remember-check">
                        <input type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                        <label for="remember">Recordarme</label>
                    </div>
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="forgot-link">
                            ¿Olvidaste tu contraseña?
                        </a>
                    @endif
                </div>

                <button type="submit" class="login-button">
                    INICIAR SESIÓN
                </button>
            </form>
        </div>
    </div>
</div>

<style>
    .login-container {
        display: flex;
        min-height: 100vh;
        background: #4a1c1c;
    }

    .logo-section {
        position: relative;
        background-color: #4a1c1c;
    }

    .form-section {
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 2rem;
        background: #4a1c1c;
    }

    .form-container {
        width: 100%;
        max-width: 400px;
        padding: 2rem;
        background: white;
        border-radius: 10px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .input-group {
        margin-bottom: 1.25rem;
    }

    .input-group input {
        width: 100%;
        padding: 0.875rem 1rem;
        border: 1px solid #E5E7EB;
        border-radius: 8px;
        font-size: 1rem;
        transition: all 0.2s ease;
        background: white;
        color: #4a1c1c;
    }

    .input-group input:focus {
        outline: none;
        border-color: #ff9933;
        box-shadow: 0 0 0 2px rgba(255, 153, 51, 0.2);
    }

    .input-group input::placeholder {
        color: #6B7280;
    }

    .options-group {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.5rem;
    }

    .remember-check {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .remember-check input[type="checkbox"] {
        width: 1rem;
        height: 1rem;
        border: 1px solid #E5E7EB;
        border-radius: 4px;
    }

    .remember-check label {
        color: #4a1c1c;
        font-size: 0.9rem;
    }

    .forgot-link {
        color: #ff6633;
        text-decoration: none;
        font-size: 0.9rem;
        transition: color 0.2s ease;
    }

    .forgot-link:hover {
        color: #ff5522;
        text-decoration: underline;
    }

    .login-button {
        width: 100%;
        padding: 0.875rem;
        background: linear-gradient(45deg, #ff6633 0%, #ff9933 100%);
        border: none;
        border-radius: 8px;
        color: white;
        font-size: 0.95rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        text-transform: uppercase;
    }

    .login-button:hover {
        background: linear-gradient(45deg, #ff5522 0%, #ff8822 100%);
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(255, 102, 51, 0.2);
    }

    .error-message {
        color: #dc2626;
        font-size: 0.875rem;
        margin-top: 0.5rem;
        display: block;
    }

    @media (max-width: 768px) {
        .login-container {
            flex-direction: column;
        }

        .logo-section {
            min-height: 30vh;
        }

        .form-container {
            padding: 1.5rem;
            margin: 1rem;
        }
    }

    .alert {
        position: fixed;
        top: 20px;
        left: 50%;
        transform: translateX(-50%);
        padding: 15px 30px;
        border-radius: 8px;
        font-size: 1rem;
        font-weight: 500;
        z-index: 1000;
        animation: slideDown 0.5s ease-out;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }

    .alert-success {
        background-color: #4CAF50;
        color: white;
        border: 1px solid #45a049;
    }

    .alert-error {
        background-color: #f44336;
        color: white;
        border: 1px solid #da190b;
    }

    @keyframes slideDown {
        from {
            transform: translate(-50%, -100%);
            opacity: 0;
        }
        to {
            transform: translate(-50%, 0);
            opacity: 1;
        }
    }
</style>
@endsection