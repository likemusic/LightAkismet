[Версия на русском (оригинал)](README.md)

# Warning!!!

This library was written in 2013. The information described here can be (veratnet all) is deprecated. Code posted as an example of my code for job search. Chances are that now on [Packagist](https://packagist.org/search/?q=akismet you can find new and more convenient and high-quality library for working with Akismet.


# LightAkismet

Light Akismet Library is intended for simple interaction with antispam-service [Akismet](http://akismet.com/), written in PHP.


## About Akismet service

Akismet service represents a server, that helps to check (verify) if the comment is a spam, and send spam containing message to add it in the base. Also it's possible to report the server, that some comments were defined as spam by mistake. All these features allow many people to use it, helping each other simultaneously.

The service has several tariff plans, including the free one. More details can be found here https://akismet.com/signup/.

To work with the service you need to get a special key to call (access) the server. You can get the key free of charge on the Akismet page.


## About LightAkismet library

The library was written for simple interaction with the Akismet service. It was written, as all the existing realizations didn't meet all the requirements. Realizations that already existed didn't suit, as they were in some way limited, out-of-date, complicated and inflexible. We recommend to become familiar with the "Comparison of LightAkismet Library with other realizations (Akismet Php4, Akismet Php5, Microakismet)".

The advantage of the library is configuration flexibility and usability.

The whole library consists of 3 classes only.

* `AkismetComment` - is intended for describing the features of the comment, that is transferred to the server function.
* `AkismetServiceSingleton` - a singleton, that contains all methods of Akismet service. To call these methods you need to install all necessary parameters, such as comment's features, the key to work with API, and the value for UserAgent field http-header of server call.
* `AkismetService` - class, that allows to set default values for all parameters, that are needed to work with server, while creating the class and during the process of work with the class. It allows to set only actually changeable parameters in the future.

Besides, AkismetService class virtually repeats functionality of AkismetServiceSingleton class. It's difference: it contains predefined  values of parameters, required for work with Akismet server.


### AkismetComment

This class is intended for representation of the checked comment's attributes. It contains components of the same name with the parameters, transferred to the Akismet server, and described in the [official documentation](https://akismet.com/development/api/),  what is naturally very convenient  when it's used.

Instance of this class can be easily created on basis of associative massive, in which keys correspond with the names of parameters, transferred to the server.

    $Object = new AkismetComment( $Array );

This will help you to go over to using it with the existing code, where the comment's parameters can be extracted in the form of array (for example, from the database).


### AkismetServiceSingleton

Contains only 3 functions, that  correspond  to the functions, described in the Akismet documentation.
* `checkComment` - checks, if the comment is a spam.
* `submitSpam` - sends a spam comment  to the server.
* `submitHam` - sends a comment,  mistakenly defined as spam, to the server.

The main difference from the names of server functions - function `checkComment``, used for server function [comment-check](https://akismet.com/development/api/#comment-check) - is made for unification - the first word of all functions is a verb.

To use the functions of this class one should set (specify, assign) all the necessary parameters, both  for the comment itself, and for the http-header request and key for work with api server.

This class is used by AkismetService class. In most cases you won't need to work with it directly, as it doesn't allow to set default values for the variables and is intended for the direct work with the server. Using AkismetService class is likely to be more convenient variant for your needs.


### AkismetService

Like [AkismetServiceSingleton](#akismetservicesingleton) this  class contain 3 methods, related to Akismet API. But, as already mentioned above, the difference is that it contains predefined values for parameters needed to work with the Akismet API servers.

You are likely to use it in your code, as it allows to set default values for all the parameters, that are needed for interaction with Akismet server. You can set constant values both in the builder during creation of the class, and in the process of usage with the help of `setDefaults()` method.


### Akismet API's methods required params (arguments)
Parameters description can be found on [Akismet documentation](http://akismet.com/development/api/).

LightAkismet Library classes don't check if all necessary parameters are installed before server's functions call, because server's behavior doesn't match with the one described in the documentation at the moment.

Table, showing the difference of information in the official documentation and real server's behavior, is situated below.

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


# Appreciation

This library sponsored and requested by [Hosting company 2by2host](http://www.2by2host.com/) and [Aimbox](http://aimbox.com/).

# Licence
The MIT License (MIT).
