import {Disclosure} from '@headlessui/react'
import Content from "@/components/Content";
import Image from 'next/image'

export default function Example() {
    return (
        <div className="min-h-full">
            <Disclosure as="nav" className="border-b border-gray-200 bg-white">
                <div className="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    <div className="flex h-16 justify-between">
                        <div className="flex">
                            <div className="flex flex-shrink-0 items-center">
                                <Image
                                    src="/logo.svg"
                                    width={500}
                                    height={500}
                                    alt="Picture of the author"
                                    className="block h-8 w-auto"
                                />
                            </div>
                            <div className="hidden sm:-my-px sm:ml-6 sm:flex sm:space-x-8">
                            </div>
                        </div>
                    </div>
                </div>
            </Disclosure>

            <div className="py-10">
                <header>
                    <div className="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                        <h1 className="text-3xl font-bold leading-tight tracking-tight text-gray-900">Checkout</h1>
                    </div>
                </header>
                <main>
                    <div className="mx-auto max-w-7xl">
                        <Content/>
                    </div>
                </main>
            </div>
        </div>
    )
}
