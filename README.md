[English version](README.en.md)
# Важно!!!

Библиотека была написана в 2013 году. Информация описанная здесь может быть (верятнее всего) устарела. Код выложен в качестве примера моего кода для поиска работы. Скорее всего, что сейчас на [Packagist](https://packagist.org/search/?q=akismet) вы сможете найти более новые и удобные и качественные библиотеки для работы с Akismet.

# LightAkismet

LightAkismet - библиотека написанная на PHP предназначена для простого взаимодействия с антиспам-сервисом [Akismet](https://akismet.com/).


## О сервисе Akismet

Сервис Akismet представляет собой сервер с помощью которого можно проверить является ли комментарий спамом, а также послать сообщения содержащее спам для добавление его в базу. Также можно сообщить серверу что некоторые комментарии ошибочно определены как спам. Все это вместе позволяет использовать его многим людям, одновременно помогая друг-другу.

Сервис имеет несколько тарифных планов, включая бесплатный. Более подробно с ними можно познакомиться [на сайте](https://akismet.com/plans/).

Для работы с сервисом необходимо получить специальный ключ, который необходим для обращения к серверу. Получить ключ можно бесплатно [на сайте Akismet](https://akismet.com/).


## О библиотеке LightAkismet

Библиотека была написана для простого взаимодействия с сервисом Akismet, т.к. существующие реализации не удовлетворяли всем необходимым в работе требованиям. Они не устраивали, т.к. были в чем-то ограничены, в чем-то устаревши, в чем-то сложны и не гибки. Рекомендую ознакомится со [Cравнением бибилотеки LightAkismet с другими реализациями (Akismet Php4, Akismet Php5, Microakismet](compare.md).

Преимуществом библиотеки является гибкость настройки и использования в сочетании с простотой.

Вся библиотека состоит всего из 3-х классов.

* `AkismetComment` - предназначен для описания свойств комментария передаваемого в функции сервера.
* `AkismetServiceSingleton` - класс-одиночка, содержащий все методы сервиса Akismet, для вызова которых необходимо установить все необходимые параметры, такие как свойства комментария, ключ для работы с API, а также значение для поля UserAgent http-заголовка запроса к серверу.
* `AkismetService` - класс, позволяющий задать значения по умолчанию для всех параметров необходимых для работы с сервером Akismet, как при создании класса, так и в процессе работы с ним. Это позволяет в дальнейшем при использовании задавать только действительно изменяющиеся параметры. То есть класс AkismetService практически повторяет функциональность класса AkismetServiceSingleton. Но его отличие заключается в том, что он содержит предопределенные значения параметров, необходимых для работы с API сервера Akismet.


### AkismetComment

Этот класс предназначен для представления свойств проверяемого комментария. Он содержит поля одноименные с параметрами передаваемыми серверу Akismet, и описанными в [официальной документации](https://akismet.com/development/api/), что, естественно, очень удобно при его использовании. 

Экземпляра этого класса может быть легко создан на основе ассоциативного массива в котором ключи соответствуют именам параметров передаваемых на сервер.

    $Object = new AkismetComment( $Array );

Это позволит вам легко перейти на его использование с уже существующим кодом, где параметры комментария могут извлекаться в виде массива (например из БД).


### AkismetServiceSingleton

Содержит всего 3 функции, соответствующие функциям описанным в документации Akismet.

* `checkComment` - проверяет является ли комментарий спамом.
* `submitSpam` - отправляет на сервер комментарий являющийся спамом.
* `submitHam` - отправляет на сервер комментарий, ошибочно определенный как спам.

Единственное отличие от имент серверных функций - функция `checkComment` используемая для серверной функции [comment-check](https://akismet.com/development/api/#comment-check). Это сделано для унификации: первое слово всех функций - глагол.

Для использования функций этого класса необходимо задать абсолютно все обязательные параметры, как самого комментария, так и http-заголовка запроса и ключ для работы с api сервера Akismet.

Этот класс используется классом `AkismetService`. В большинстве случаев вам не понадобиться работать с ним напрямую, т.к. он не позволяет задать значения по умолчанию для переменных и предназначен для непосредственной работы с сервером. Скорее всего, что для Ваших нужд более удобным вариантом будет использование класса `AkismetService`.


### AkismetService

Как и класс [AkismetServiceSingleton](#akismetservicesingleton) содержит 3 функции, соответствующие функциям описанным в документации Akismet. Но, как уже было сказано выше, его отличие заключается в том, что он содержит предопределенные значения параметров, необходимых для работы с API сервера Akismet.

Скорее всего в своем коде вы будете использовать именно его, т.к. он позволяет задать значения по умолчанию абсолютно для всех параметров, необходимых для взаимодействия с api-сервером Akismet. Задать неизменные значения можно как в конструкторе при создании класса, так и в процессе использования с помощью метода setDefauls();

### Обязательные параметры методов API Akismet
Описание параметров можно найти на сайте [Akismet](http://akismet.com/development/api/).

Классы библиотеки LightAkismet перед вызовом серверных функций не проверяют установлены ли все обязательные параметры, т.к. на данный момент поведение сервера не совпадает с описанным в документации.

Ниже расположена таблица показывающая отличия информации в офф. документации и реальным поведением сервера.

<table border="1">
    <tbody>
        <tr>
            <td rowspan="3" style="text-align: center;">Name</td>
            <td colspan="3" style="text-align: center;">Required</td>
        </tr>
        <tr>
            <td style="text-align: center;">By documentation</td><td colspan="2" style="text-align: center;">Really</td>
        </tr>
        <tr>
            <td style="text-align: center;">comment-check,<br>submit-spam,<br>submit-ham</td>
            <td style="text-align: center;">comment-check</td>
            <td style="text-align: center;">submit-spam,<br>submit-ham</td>
        </tr>
        <tr>
            <td>blog</td>
            <td style="text-align: center;"> + </td>
            <td style="text-align: center;"> + </td>
            <td style="text-align: center;">&nbsp;</td>
        </tr>
        <tr>
            <td>user_ip</td>
            <td style="text-align: center;"> + </td>
            <td style="text-align: center;"> + </td>
            <td style="text-align: center;"> &nbsp;</td>
        </tr>
        <tr>
            <td> user_agent</td>
            <td style="text-align: center;"> + </td>
            <td style="text-align: center;"> &nbsp; </td>
            <td style="text-align: center;"> &nbsp; </td>
        </tr>
        <tr>
            <td> refferer</td>
            <td style="text-align: center;"> &nbsp; </td>
            <td style="text-align: center;"> &nbsp; </td>
            <td style="text-align: center;"> &nbsp; </td>
        </tr>
        <tr>
            <td> permalink</td>
            <td style="text-align: center;"> &nbsp;</td>
            <td style="text-align: center;"> &nbsp;</td>
            <td style="text-align: center;"> &nbsp;</td>
        </tr>
        <tr>
            <td> comment_type</td>
            <td style="text-align: center;"> &nbsp;</td>
            <td style="text-align: center;"> &nbsp;</td>
            <td style="text-align: center;"> &nbsp;</td>
        </tr>
        <tr>
            <td> comment_author</td>
            <td style="text-align: center;"> &nbsp; </td>
            <td style="text-align: center;"> &nbsp; </td>
            <td style="text-align: center;"> &nbsp; </td>
        </tr>
        <tr>
            <td> comment_author_email</td>
            <td style="text-align: center;"> &nbsp;</td>
            <td style="text-align: center;"> &nbsp;</td>
            <td style="text-align: center;"> &nbsp;</td>
        </tr>
        <tr>
            <td> comment_author_url</td>
            <td style="text-align: center;"> &nbsp;</td>
            <td style="text-align: center;"> &nbsp;</td>
            <td style="text-align: center;"> &nbsp;</td>
        </tr>
        <tr>
            <td> comment_content</td>
            <td style="text-align: center;"> &nbsp;</td>
            <td style="text-align: center;"> &nbsp;</td>
            <td style="text-align: center;"> &nbsp;</td>
        </tr>
    </tbody>
</table>

# Благодарности

Библиотека оплачена и создана благодаря [Хостинговой компании 2by2host](http://www.2by2host.com/) и [Aimbox](http://aimbox.com/).

# Licence
The MIT License (MIT).
