class Comment < ApplicationRecord
  belongs_to :user
  belongs_to :post, counter_cache: false

  validates :body, presence: true, length: { maximum: 500 }

  scope :recent, -> { order(created_at: :desc) }
end
