# Сравнение LightAkismet c существующими библиотеками

## Сводная таблица

Легенда таблицы:
* `-` - нет (плохо)
* `+/-` - частично
* `+` - да (хорошо)


Название параметра | AkismetPhp4 | AkismetPhp5 | Microakismet | LightAkismet
------------------ |:-----------:|:-----------:|:------------:|:-------------:
Название ключей массива описывающего комментарий, или функций для их установки соответствуют именам параметров передаваемых на сервер, описанных в официальной документации Akismet | - | +/- | + | + 
Возможность проверить одним объектом много комментариев | - | + | + | +
Обработка ошибок с помощью выбрасывания исключений | - | + | - | +
Возможность управлять автоматической проверкой ключа в конструкторе или при вызове функций | - (всегда включено, в конструкторе) | -
(всегда выключено) | - (всегда вызывается при вызове check()) | + (определается параметром конструктора $AutoVerifyKey, по умолчанию false - выключено) 
Параметры (свойства) комментариев и сервис для работы с ними представлены разными сущностями (объектами) | - | - | + | +
Возможность использовать ассоциативный массив в качестве описания проверяемого комментария | + | - | + | +/- (легко создавая из него объект: $Object = new AkismetComment($Array))
Возможность использовать объек в качестве описания проверяемого комментария | - | - | - | +
Отсутствие в коде лишних внутренних классов используемых основными используемыми классами, простая архитектура. | + | - | + | +
Отсутствие в коде оператора подавления ошибок @, который при использовании усложняет отладку и определение проблемных мест при ошибках в работе php-скрипта. | - | - | + | +
Возможность задать необходимый заголовок UserAgent в http-запросе на сервер Akismet в соответствии с документацией | - (всегда "Akismet PHP4 Class") | - (всегда "Akismet PHP5 Class 0.5 &#124; Akismet/1.11") | +/- (всегда $this->akismet_ua." &#124; vanhegan.net-akismet.inc.php/1.0"")" | +
Возможность задать значения по умолчанию для параметров комментариев | - | - | - | +
Проверка кода ответа Http-сервера (должен быть 200) | - | - | - | +
В возбуждаемых исключениях содержатся сообщения от сервера Akismet (из тела ответа или из http-заголовка "'X-akismet-debug-help") | - | - | - | +


## Недостатки существующих библиотек

### Akismet Php4

#### Название ключей массива (представляющего комментарий) не соответствует именам параметров передаваемых на сервер и описанных в [документации](https://akismet.com/development/api/).
Пример:
```php
$comment = array(
    'author'    => 'viagra-test-123', // соответствует comment_author
    'email'     => 'test@example.com',// соответствует comment_author_email
    'website'   => 'http://www.example.com/', // соответствует blog
    'body'      => 'This is a test comment', // соответствует comment_content
    'permalink' => 'http://yourdomain.com/yourblogpost.url',
);
```
Т.е. после чтения [оффициальной документации Akismet](https://akismet.com/development/api/) надо в коде еще переводить имена параметров на язык используемой библиотеки.


#### Для проверки каждого комментария нужно создавать новый объект.
Пример:
```php
$Comments = array();//массив с комментариями
foreach( $Comments as $Comment )
{
  $akismet = new Akismet('http://www.yourdomain.com/', 'YOUR_WORDPRESS_API_KEY', $comment);
}
```
Т.е. если в необходимо проверить большой список комментариев, то необходимо создать много объектов, в которых только один элемент, по сути, будет отличаться. Намного более привлекательна в этом случае возможность инициализации одного объекта с необходимыми значениями и вызов функции этого объекта для каждого комментария, как это реализовано в LightAkismet, т.к. в этом случае для каждого промеряемого комментария не будет выполнять код инициализации класса (в конструкторе):

```php
$Comments = array();//массив с комментариями
$akismet = new AkismetService('YOUR_WORDPRESS_API_KEY', 'YouUserAgentHeader', 'http://www.yourdomain.com/');
foreach( $Comments as $Comment )
{
  $akismet->checkComment( $comment );
}
```

В случае же, если нужно проверить только один комментарий, можно воспользоваться синглтоном AkismetServiceSingleton из LightAkismet:
```php
AkismetServiceSingleton::getInstance()->checkComment ( $ApiKey, $HttpUserAgent, $Comment, $BlogUrl );
```

#### Устаревший стиль работы с ошибками и исключениями
```php
if( $akismet->errorsExist() ) {
    echo"Couldn't connected to Akismet server!";
} else {
    if($akismet->isSpam()) {
        echo"Spam detected";
    } else {
        echo"yay, no spam!";
    }
}
```

Классы из LightAkismet умеют выбрасывать исключения, что, естественно, намного упрощает разработку, и более точно позволяет определить причину её вызвавшую. Состояние класса независимо от того какие функции вызывались или не вызывались до этого.
``` php
try {
    if( $akismet->checkComment( $CommentValues ) ) {
        echo"Spam detected";
    } else {
        echo"yay, no spam!";
    }
} catch (Exception $e) {
    echo $e->getMessage();
}
```

#### Автоматическая проверка ключа в конструкторе
В конструкторе класса происходит автоматический вызов проверки ключа:
```php
// Check if the API key is valid
if(!$this->_isValidApiKey($apiKey)) {
    $this->setError(AKISMET_INVALID_KEY, "Your Akismet API key is not valid.");
}
```

Это в большинстве случаев излишнее, т.к. значение ключа не изменяется между вызовами методов сервера.

Вызов этого метода довольно затратный т.к. для его выполнения, как и для всех других функций класса, необходимо установление связи с сервером, посылка ключа и ожидание ответа сервера.

В классе из AkismetService из LightAkismet по умолчанию не вызывается функция проверки ключа в конструкторе. Однако при необходимости её можно включить установив параметр конструктора `$AutoVerifyKey = true`.



### Недостатки Akismet Php5

#### Смешаны в одном классе функции для задания параметров комментария и функции для рабты с API Akismet.
Пример использования из документации:
```php
$akismet = new Akismet('http://www.example.com/blog/', 'aoeu1aoue');
...
$akismet->setCommentAuthor( $name );
$akismet->setCommentAuthorEmail( $email );
$akismet->setCommentAuthorURL( $url );
$akismet->setCommentContent( $comment );
$akismet->setPermalink( 'http://www.example.com/blog/alex/someurl/' );

if( $akismet->isCommentSpam() )
    // store the comment but mark it as spam (in case of a mis-diagnosis)
else
    // store the comment normally
```

В LighAkismet сервис работы с комментариями и сами комментарии представлены различными классами. AkismetService - представляет собой сервис для работы с API Akismet. При вызове его методов в качестве параметров они принимают параметры комментария, которй может быть представлен как ассоциативный массив (ключи которого совпадают с именами параметров передаваемых на сервер согласно официальной документации) или как объект объект AkismetComment.


Аланалогичный код на LightAkismet может выглядеть, например, так:
```php
$akismet = new Akismet( 'aoeu1aoue', 'LightAkismetAgent', $ApiKey);
$CommentParams = new AkismetComment();
...
$CommentParams->CommentAuthor = $name;
$CommentParams->AuthorEmail = $email;
$CommentParams->setCommentAuthorURL = $url;
$CommentParams->setCommentContent = $comment;
$CommentParams->Permalink = 'http://www.example.com/blog/alex/someurl/';
if( $akismet->checkComment( $CommentParams ) )
    // store the comment but mark it as spam (in case of a mis-diagnosis)
else
    // store the comment normally
```

#### Нет возможности использовать ассоциативный массив в качестве описания проверяемого комментария

Например при извлечении параметров из БД удобно сделать проверку комментария одним вызовом, а не целой серией функций установления параметров.

Код на AkismetPhp5:
```php
$CommentValues = GetCommentFromDb();
$akismet->setCommentAuthor( CommentValues['comment_author'] );
$akismet->setCommentAuthorEmail( CommentValues['comment_author_email'] );
$akismet->setCommentAuthorURL( CommentValues['comment_author_url'] );
$akismet->setCommentContent( CommentValues['comment_content'] );
$akismet->setCommentType( CommentValues['comment_type'] );
$akismet->setCommentUserAgent( CommentValues['user_agent'] );
$akismet->setPermalink( CommentValues['permalink'] );
$akismet->setReferrer( CommentValues['referrer'] );
$akismet->setUserIP( CommentValues['user_ip'] );
$IsSpam = $akismet->isCommentSpam();
```

Благодаря тому, что конструктор класса AkismetComment, представляющий параметры комментария, принимает первым параметром ассоциативный массив, аналогичный код с использованием LightAkismet выглядит намного лаконичнее:
```php
$CommentValues = GetCommentFromDb();
$IsSpam = $akismet->checkComment( new AkismetComment( $CommentValues ) );
```

#### Излишние классы в коде (сложная, слишком навороченная архитектура)

Помимо непосредственно используемого класса Akismet определены еще:
 - Интерфейсы
  - AkismetRequestFactory
  -	AkismetRequestSender
- Классы
  - SocketWriteRead
  - SocketWriteReadFactory

LightAkismet определяет только реально используемые классы:
 - `AkismetComment` - класс свойств комментария. Может быть легко создан на основе ассоциативного массива;
 - `AkismetService` - класс содержащий предустановленные значения параметров передаваемых на сервер, с помощью которго в большинстве случаев должно происходить взаимодействие с сервером Akismet.
 - `AkismetServiceSingleton` - класс-одиночка (singleton) для непосредственного вызова методов сервера Akismet; Используется классом AkismetService а также может быть использован непосредственно с установлением всех необходимых параметров для запроса к серверу Akismet.


### MicroAkismet

Нет возможности задать или изменить UserAgent для http-запросов.