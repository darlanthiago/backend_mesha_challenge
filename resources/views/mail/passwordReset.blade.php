@component('mail::message')
# Olá, {{ $user->name }}

Você está recebendo esse email, porque foi solicitado que resetasse a senha da sua conta.


@component('mail::button', ['url' => $data['link'] . 'password-reset?token=' . $data['token']])
    Clique aqui para alterar
@endcomponent

### *Este link tem a validade de 1 hora.

@endcomponent
