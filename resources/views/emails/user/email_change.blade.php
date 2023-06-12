<?php
/** @var \App\Models\Users\UserEmailConfirmation $emailConfirmation */
?>
<x-mail::message>
# Подтверждение адреса электронной почты
Здравствуйте, {{ $emailConfirmation->user->fullName }}!

Чтобы привязать адрес электронной почты {{ $emailConfirmation->new_email }} к Вашей учётной записи, его необходимо подтвердить. Для этого нажмите кнопку ниже:

<x-mail::button :url="$emailConfirmation->getConfirmationLink()">
Подтвердить адрес почты
</x-mail::button>

Если Вы не запрашивали смену адреса электронной почты можете прост проигнорировать это письмо.

С уважением, {{ config('app.name') }}
</x-mail::message>
