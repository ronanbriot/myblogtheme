@extends('layouts.app')

@section('content')
<style>
/* Styles Safari-compatibles */
.login-container {
    -webkit-background-size: cover;
    background-size: cover;
}

.login-card {
    -webkit-box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
    -webkit-border-radius: 16px;
    border-radius: 16px;
}

.login-input {
    -webkit-appearance: none;
    -moz-appearance: none;
    appearance: none;
    -webkit-box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
    box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
    -webkit-border-radius: 12px;
    border-radius: 12px;
}

.login-button {
    -webkit-appearance: none;
    -moz-appearance: none;
    appearance: none;
    -webkit-border-radius: 12px;
    border-radius: 12px;
    -webkit-transition: all 0.2s ease;
    transition: all 0.2s ease;
}

.login-button:hover {
    -webkit-transform: scale(1.02);
    transform: scale(1.02);
}

.login-button:active {
    -webkit-transform: scale(0.98);
    transform: scale(0.98);
}
</style>
<div class="min-h-screen bg-gray-50 flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8 login-container" style="background: linear-gradient(135deg, #f5f3ff 0%, #ffffff 50%, #faf5ff 100%);">
  <div class="max-w-md w-full">
    <!-- Card Container -->
    <div class="bg-white rounded-2xl p-8 border border-gray-100 login-card">
      <!-- Header -->
      <div class="text-center mb-8">
        <div class="mx-auto h-16 w-16 rounded-full flex items-center justify-center mb-4" style="background: linear-gradient(135deg, #8b5cf6 0%, #9333ea 100%);">
          <svg class="h-8 w-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
          </svg>
        </div>
        <h2 class="text-3xl font-bold text-gray-900 mb-2">
          Bienvenue !
        </h2>
        <p class="text-gray-600">
          Connectez-vous pour accéder à votre espace
        </p>
      </div>
      
      <!-- Error Message -->
      @if (isset($_GET['login']) && $_GET['login'] === 'failed')
        <div class="mb-6 bg-red-50 border-l-4 border-red-400 p-4 rounded-r-lg">
          <div class="flex">
            <div class="flex-shrink-0">
              <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
              </svg>
            </div>
            <div class="ml-3">
              <p class="text-sm text-red-700">
                Identifiant ou mot de passe incorrect. Veuillez réessayer.
              </p>
            </div>
          </div>
        </div>
      @endif
      
      <!-- Login Form -->
      @include('forms.login')
      
      <!-- Footer -->
      <div class="mt-8 text-center">
        <p class="text-sm text-gray-500">
          Besoin d'aide ? 
          <a href="{{ wp_lostpassword_url() }}" class="font-medium text-violet-600 hover:text-violet-500 transition-colors">
            Mot de passe oublié
          </a>
        </p>
      </div>
    </div>
  </div>
</div>
@endsection
