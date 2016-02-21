<!DOCTYPE html>
<html lang="en-US">
    <head>
        <meta charset="utf-8">
    </head>
    <body>
        <p> Following user has been registered with the system.</p>
        <div>
            <table>
                <tr>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Email</th>
                </tr>
                <tr>
                    <td>{{$user->fname}}</td>
                    <td>{{$user->lname}}</td>
                    <td>{{$user->email}}</td>
                </tr>
            </table>
        </div>
        <p>
            Stay Fit,<br> The team at Sportofit
        </p>
        <p>Thank you</p>

    </body>
</html>