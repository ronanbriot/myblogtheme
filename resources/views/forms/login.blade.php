<form name="loginform" id="loginform" action="{{ home_url('/login'); }}" method="post" class="space-y-6">
    <div class="space-y-4">
        <!-- Email Field -->
        <div>
            <label for="user_login" class="block text-sm font-semibold text-gray-700 mb-2">
                Identifiant ou adresse e-mail
            </label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"></path>
                    </svg>
                </div>
                <input 
                    type="text" 
                    name="log" 
                    id="user_login" 
                    autocomplete="username" 
                    required 
                    class="block w-full pl-10 pr-3 py-3 bg-gray-50 border-2 border-gray-200 rounded-xl placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-2 focus:ring-violet-500 focus:border-violet-500 focus:bg-white transition-all duration-200 login-input" 
                    style="box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);"
                    placeholder="votre@email.com"
                />
            </div>
        </div>
        
        <!-- Password Field -->
        <div>
            <label for="user_pass" class="block text-sm font-semibold text-gray-700 mb-2">
                Mot de passe
            </label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                    </svg>
                </div>
                <input 
                    type="password" 
                    name="pwd" 
                    id="user_pass" 
                    autocomplete="current-password" 
                    spellcheck="false" 
                    required 
                    class="block w-full pl-10 pr-3 py-3 bg-gray-50 border-2 border-gray-200 rounded-xl placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-2 focus:ring-violet-500 focus:border-violet-500 focus:bg-white transition-all duration-200 login-input" 
                    style="box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);"
                    placeholder="••••••••"
                />
            </div>
        </div>
    </div>
    
    <!-- Remember Me -->
    <div class="flex items-center">
        <input 
            name="rememberme" 
            type="checkbox" 
            id="rememberme" 
            class="h-4 w-4 text-violet-600 focus:ring-violet-500 border-gray-300 rounded" 
            value="forever" 
        />
        <label for="rememberme" class="ml-2 block text-sm text-gray-600">
            Se souvenir de moi
        </label>
    </div>
    
    <!-- Submit Button -->
    <div>
        <button 
            type="submit" 
            name="wp-submit" 
            id="wp-submit" 
            class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-semibold rounded-xl text-white focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-violet-500 transition-all duration-200 login-button"
            style="background: linear-gradient(135deg, #7c3aed 0%, #9333ea 100%); box-shadow: 0 4px 14px 0 rgba(124, 58, 237, 0.4);"
            onmouseover="this.style.background='linear-gradient(135deg, #6d28d9 0%, #7c3aed 100%)'; this.style.transform='scale(1.02)';"
            onmouseout="this.style.background='linear-gradient(135deg, #7c3aed 0%, #9333ea 100%)'; this.style.transform='scale(1)';"
            onmousedown="this.style.transform='scale(0.98)';"
            onmouseup="this.style.transform='scale(1.02)';"
        >
            <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                <svg class="h-5 w-5 text-violet-200 group-hover:text-violet-100" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
                </svg>
            </span>
            Se connecter
        </button>
        <input type="hidden" name="redirect_to" value="{{ home_url('/'); }}" />
        <input type="hidden" name="testcookie" value="1" />
    </div>
</form>