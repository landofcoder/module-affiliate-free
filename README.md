# Magento 2 Affiliate Free Version

    ``landofcoder/module-affiliate-free``
    Link: https://landofcoder.com/magento-2-affiliate-extension-free.html

 - [Main Functionalities](#markdown-header-main-functionalities)
 - [Pro Version](#markdown-header-pro-version)
 - [Installation](#markdown-header-installation)

## Main Functionalities
Are you looking for a perfect extension to create your affiliate program?

Affiliate marketing is one of the most important marketing tools for selling online.
Magento 2 Affiliate Extension Free helps you to drive more sales from your affiliate channels and let your affiliate earn money.
The extension is fully responsive, fast and easy for affiliate partners to join your program.

- Pay Per Lead
- Pay Per Click
- Pay Per Sale
- Pay Per New Customer’s Order
- Multiple affiliate programs
- Multi-level marketing
- Set commission, discounts
- Easy to set conditions & requirements
- Manage banner & links in one place
- Payout requirements
- Transaction management.
- Set withdrawal limits
- Manage partner's account with ease
- Mass payments
- Withdraw commissions via Paypal & Bank Transfer
- Clear and Easy To Use
- Product recommendations: Store Locator, Gift Card, Store Credit

## Pro Version
More features? Follow Pro Version here:
Link: https://landofcoder.com/magento-2-affiliate-extension.html

## Installation
\* = in production please use the `--keep-generated` option

### Type 1: Zip file

 - Unzip the zip file in `app/code/Lof`
 - Enable the module by running `php bin/magento module:enable Lof_Affiliate`
 - Apply database updates by running `php bin/magento setup:upgrade`\*
 - Flush the cache by running `php bin/magento cache:flush`

### Type 2: Composer

 - Make the module available in a composer repository for example:
    - private repository `repo.magento.com`
    - public repository `packagist.org`
    - public github repository as vcs
 - Add the composer repository to the configuration by running `composer config repositories.repo.magento.com composer https://repo.magento.com/`
 - Install the module composer by running `composer require landofcoder/module-affiliate-free`
 - enable the module by running `php bin/magento module:enable Lof_Affiliate`
 - apply database updates by running `php bin/magento setup:upgrade`\*
 - Flush the cache by running `php bin/magento cache:flush`
