# Display a cofirm prompt before submitting a form (e.g. when deleting)
# @param string message The message to confirm (e.g. Are you sure...?)
$.fn.confirmSubmit = (message) ->
  if confirm(message)
    $(this).submit()

# To use, simply pass flash_message='...' with the view/ redirect
# To make the message stay on the screen, pass flash_message_important=true

# our custom alert flash message box
$(".alert")
  .not(".alert-important")
  .delay(3000)
  .slideUp(300)
