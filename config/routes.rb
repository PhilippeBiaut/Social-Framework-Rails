Rails.application.routes.draw do
  # Authentication (Rails 8 generator) + self-service registration
  resource  :session
  resources :passwords, param: :token
  resource  :registration, only: %i[new create]

  # Current user's own profile settings
  resource  :profile, only: %i[edit update]

  # People / social graph
  resources :users, param: :username, only: %i[index show] do
    resource :follow, only: %i[create destroy]
    member do
      get :following
      get :followers
    end
  end

  # Posts and their nested engagement
  resources :posts, only: %i[index show new create destroy] do
    resource  :like,     only: %i[create destroy]
    resources :comments, only: %i[create destroy]
  end

  # Reveal health status on /up
  get "up" => "rails/health#show", as: :rails_health_check

  # The feed is the home page
  root "posts#index"
end
