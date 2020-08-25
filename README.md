# Ploi WordPress Plugin

This is the source code of the Ploi WordPress plugin. This plugin makes it easy to flush OPcache and FastCGI cache (if enabled).

## Getting Started

Download the plugin from your WordPress installation, on the plugin page.

You can either do this by searching in the WordPress plugin page, or uploading the ZIP downloaded from this repository.

## Security notice

While this plugin is an awesome tool to manage your site with, it comes with security vulnerabilities because it is still WordPress.

The plugin uses a cipher to encrypt your API token from ploi.io, this does not guarantee hackers cannot read the key. The installation itself
saves the key encrypted, but the system itself is also possible to decrypt it.

Be **carefully** aware of this fact, make sure to completely protect your WordPress site to prevent hackers from stealing your data.

If in any case it has been hacked, make sure to revoke the Ploi API token in your profile: https://ploi.io/profile/api-keys

**Ploi cannot be held responsible for a stolen API key and the consequences of a stolen API key, it is up to you to keep your installation protected.**

## Contributing

PR's to improve this plugin is more than welcome. In case of any big adjustments or breaking changes you might want to discuss
beforehand with the Ploi team to make sure your PR won't get rejected for nothing.

## Authors

* **Giorgos Tsarmpopoulos** - *Initial work* - [tsarbo](https://github.com/tsarbo)
* **Dennis Smink** - *Maintainer* - [cannonb4ll](https://github.com/cannonb4ll)

## License

This project is licensed under the MIT License - see the [LICENSE](https://github.com/ploi-deploy/ploi-wordpress-plugin/blob/master/LICENSE) file for details