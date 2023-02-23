Laravel10 Base プロジェクト
====================


<!-- @import "[TOC]" {cmd="toc" depthFrom=1 depthTo=6 orderedList=false} -->

<!-- code_chunk_output -->

- [前提条件](#-前提条件)
- [環境](#-環境)
  - [バージョン](#-バージョン)
  - [コンテナ](#-コンテナ)
  - [機能](#-機能)
- [構築](#-構築)
  - [管理者向け](#-管理者向け)
  - [開発者向け](#-開発者向け)
- [勉強内容](#-勉強内容)
  - [参考資料](#-参考資料)
  - [検索コマンド](#-検索コマンド)

<!-- /code_chunk_output -->

## 前提条件

* docker, docker-composeがインストールされていること
* bashが利用できること

## 環境

### バージョン

| プログラム | バージョン |
| ---------- | ---------- |
| php        | 8.2.3      |
| laravel    | 10.0.3     |
| mysql      | 8.0.xx     |

### コンテナ

| コンテナ |            機能            |
| -------- | -------------------------- |
| php      | Appサーバ                  |
| mysql    | DBサーバ                   |
| adminer  | DB WebGUI                  |
| mailpit  | メールサーバ/メール WebGUI |

### 機能

|   機能    |          URL           |
| --------- | ---------------------- |
| アプリ    | http://localhost/      |
| DB GUI    | http://localhost:8080/ |
| Mail GUI  | http://localhost:8025/ |

## 構築

### 管理者向け

プロジェクトの初期構築手順は以下を参照
* [初期構築](./docs/init-project.md)

### 開発者向け

リポジトリをclone後, run.shシェルにて環境構築してください

```bash
# リポジトリ clone
git clone [リポジトリURL]

# 初期構築
./run.sh init
```

※`run.sh`に開発によく使うコマンドが定義されています。

---

## 勉強内容

サンプルソースを通じて以下を経験する

- レイヤーの有無によるテスト性(作りやすさ、直しやすさ）の違い
- レイヤーの有無による保守性の違い

### 参考資料

* 予防に勝る防御なし
  - https://speakerdeck.com/twada/growing-reliable-code-phperkaigi-2022

### 検索コマンド

```bash
# BugReportController::index()
curl -X GET localhost/bug-reports/ | jq

# BugReportController::all()
curl -X GET localhost/bug-reports/all | jq
```
