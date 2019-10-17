# iQing-export-markdown

将轻文轻小说缓存导出为 `Markdown` 文件的脚本

**注意：因为现在轻文的相关 API 正常，所以本脚本可以正常工作，但是由于轻文 API 随时都有可能死亡，从而导致本脚本失效，请尽快导出**

## 使用

> 需要： `php`, `php-curl` 

本项目仅在 `php >= 7` 的环境下测试过，不保证兼容性

将在手机储存卡目录下的 `./iQing/data/book` 下的所有文件拷至与 `index.php` 同目录下的`ori` 文件夹下

在 `index.php` 同目录下建立 `out` 文件夹

运行 `index.php` 即可

## 发现一个 Bug / 改进此项目

你可以发起 `Pull Request` 或提出 `Issue`
