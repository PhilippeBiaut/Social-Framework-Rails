class Comment < ApplicationRecord
  belongs_to :user
  # Explicit inverse: Post's `has_many :comments` carries a scope, which
  # disables automatic inverse detection and would re-query `comment.post`.
  belongs_to :post, inverse_of: :comments

  validates :body, presence: true, length: { maximum: 500 }

  scope :recent, -> { order(created_at: :desc) }
end
