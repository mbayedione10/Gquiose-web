<?php

it('returns config information', function () {
    $response = $this->getJson('/api/v1/config');

    $response->assertStatus(200)
        ->assertJsonStructure(['code', 'message']);
});

it('returns articles list', function () {
    $response = $this->getJson('/api/v1/articles');

    $response->assertStatus(200);
});

it('returns faqs', function () {
    $response = $this->getJson('/api/v1/faqs');

    $response->assertStatus(200);
});

it('returns thematiques', function () {
    $response = $this->getJson('/api/v1/thematiques');

    $response->assertStatus(200);
});

it('returns forum messages', function () {
    $response = $this->getJson('/api/v1/forum');

    $response->assertStatus(200);
});

it('returns videos list', function () {
    $response = $this->getJson('/api/v1/videos');

    $response->assertStatus(200);
});

it('returns rubriques with articles', function () {
    $response = $this->getJson('/api/v1/rubriques');

    $response->assertStatus(200);
});

it('returns alert workflow options', function () {
    $response = $this->getJson('/api/v1/alertes/workflow-options');

    $response->assertStatus(200);
});
