# VismaConnect


## About
There are many great and brilliant ways to integrate Visma Connect into your own PHP Software.
This sample Class gives you the optimal integration, without major work needed.

Will suggest you check out my other work with DineroConnect - and how to use these related classes.

### Howto get started

You need to obtain a Visma Connect Client ID and Visma Connect Client Secret, before being able to connect to Visma Authorize.

```
$visma = new VismaConnect("","Visma Connect Client ID ","Visma Connect Client Secret","Visma Auth url");
$visma->UpdateScope("offline_access");
$url = $visma->requestUrl();
header("Location: $url");
```