<?php

it('redirects the home page to the login screen', function () {
    $response = $this->get('/');

    $response->assertRedirect(route('login'));
});
