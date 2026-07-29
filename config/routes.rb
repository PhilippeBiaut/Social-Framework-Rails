Rails.application.routes.draw do
  # Authentication. Only the actions that exist are declared, so no route can
  # resolve to a missing action.
  get  "login",  to: "sessions#new",     as: :new_session
  post "login",  to: "sessions#create",  as: :session
  post "logout", to: "sessions#destroy", as: :logout

  get  "register", to: "registrations#new",    as: :new_registration
  post "register", to: "registrations#create", as: :registration

  get   "forgot-password",         to: "passwords#new",    as: :new_password
  post  "forgot-password",         to: "passwords#create", as: :passwords
  get   "reset-password/:token",   to: "passwords#edit",   as: :edit_password
  patch "reset-password/:token",   to: "passwords#update", as: :password

  # Current user's own profile settings
  get   "settings/profile", to: "profiles#edit",   as: :edit_profile
  patch "settings/profile", to: "profiles#update", as: :profile

  # People / social graph
  get "people", to: "users#index", as: :users

  resources :users, param: :username, only: %i[show], path: "users" do
    resource :follow, only: %i[create destroy]
    member do
      get :following
      get :followers
    end
  end

  # Posts and their nested engagement
  resources :posts, only: %i[index show create destroy] do
    resource  :like,     only: %i[create destroy]
    resources :comments, only: %i[create destroy]
  end

  # Reveal health status on /up
  get "up" => "rails/health#show", as: :rails_health_check

  # The feed is the home page
  root "posts#index"
end
