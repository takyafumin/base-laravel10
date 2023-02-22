プロジェクト初期構築
====================


<!-- @import "[TOC]" {cmd="toc" depthFrom=1 depthTo=6 orderedList=false} -->

<!-- code_chunk_output -->

- [Laravel Breezeインストール](#-laravel-breezeインストール)
- [開発用ツールインストール](#-開発用ツールインストール)
  - [IDE Helper](#-ide-helper)

<!-- /code_chunk_output -->

## Laravel Breezeインストール

```bash
# php containerに入る
make bash

# install
composer require laravel/breeze --dev

# リソースを公開
php artisan breeze:install

exit

# npmパッケージインストール & ビルド
make npm-install
make npm-dev
```

## 開発用ツールインストール

### IDE Helper

```bash
# php containerに入る
./run.sh bash

# インストール
composer require --dev barryvdh/laravel-debugbar
composer require --dev barryvdh/laravel-ide-helper

# idehelperファイル生成
php artisan ide-helper:generate
php artisan ide-helper:model --nowrite

exit
```
