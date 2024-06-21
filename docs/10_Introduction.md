## Why

I started with a project with an abstract database class. For objects I extend a base class that offers all CRUD functions and more. So I don't need to handle with sql in my custom objects. 

But this is is a non visual class.



This project offers now a web interface. 

## What it can do

An application is a pre defined set of its objects. Axels object manager can handle multiple applications.

```mermaid
flowchart TD
    OM(((fa:fa-gears Axels<br>Object manager))):::orange
    OM === A{{fa:fa-gear Config<br>Address book}}:::cfgfile
    OM === B{{fa:fa-gear Config<br>Blog}}:::cfgfile
    OM === C{{fa:fa-gear Config<br>Devices}}:::cfgfile

    subgraph " app<br>Address book"
        A --- A1(fa:fa-file-code Person):::classes
        A --- A2(fa:fa-file-code Addresses):::classes
        A --- A3(fa:fa-file-code PhoneNumbers):::classes
    end

    subgraph " "
        B --- B1(fa:fa-file-code Articles):::classes
        B --- B3(fa:fa-file-code Tags):::classes
    end

    subgraph " "
        C --- C1(fa:fa-file-code Device):::classes
        C --- C2(fa:fa-file-code OS):::classes
        C --- C3(fa:fa-file-code Applications):::classes
    end

    classDef orange fill:#e71,stroke:#a61,color:#fff
    classDef cfgfile fill:#699,stroke:#144,color:#fff
    classDef classes fill:#469,stroke:#124,color:#fff
    classDef gray fill:#eee,stroke:#aaa

```
