# Marks fields that failed validation with aria-invalid, so a screen reader
# announces the field as invalid instead of leaving the error summary as the
# only signal. Applied to every form via config.action_view.default_form_builder.
class AccessibleFormBuilder < ActionView::Helpers::FormBuilder
  FIELD_HELPERS = %i[
    text_field email_field password_field text_area
    telephone_field url_field number_field date_field
  ].freeze

  FIELD_HELPERS.each do |helper|
    define_method(helper) do |attribute, options = {}|
      super(attribute, invalid_attrs(attribute).merge(options))
    end
  end

  def file_field(attribute, options = {})
    super(attribute, invalid_attrs(attribute).merge(options))
  end

  private

  def invalid_attrs(attribute)
    return {} unless object.respond_to?(:errors) && object.errors[attribute].present?

    { "aria-invalid": "true" }
  end
end
